<?php
/**
 * by stephan scheide
 */

namespace App\Services\Ssl;


use App\Entities\Branches\Location;
use App\Entities\Ssl\SslJob;
use App\Entities\Ssl\SslJobStatus;
use App\Logging\CompositeFacade;
use App\Logging\LogFacade;
use App\Logging\LoggingClip;
use App\Repositories\Branches\LocationRepository;
use App\Repositories\Ssl\SslJobRepository;
use App\Utils\CheckBuilder;
use App\Utils\DateTimeUtils;
use App\Utils\FileUtils;
use App\Utils\QuickTemplateEngine;
use App\Utils\StringUtils;
use App\ValueObjects\LocationSslInfo;
use Illuminate\Support\Facades\DB;

class SslService
{

    const TEMPLATE_PATH_PRIVATEKEY = "/etc/letsencrypt/live/%domain%/privkey.pem";

    const TEMPLATE_PATH_FULLCHAIN = "/etc/letsencrypt/live/%domain%/fullchain.pem";

    const LETS_ENCRYPT_PATH = '/etc/letsencrypt';

    private $sslJobRepository;

    private $locationRepository;

    /**
     * @var LoggingClip $clip
     */
    private $clip;

    public static function isDomainValid($d)
    {
        if (StringUtils::isEmpty($d)) {
            return false;
        }

        if (strpos($d, 'http:') === 0) {
            return false;
        }

        if (strpos($d, 'https:') === 0) {
            return false;
        }

        if (strpos($d, 'www.') !== false) {
            return false;
        }

        if (strpos($d, ':') !== false) {
            return false;
        }

        if (strpos($d, '/') !== false) {
            return false;
        }

        if ($d[strlen($d) - 1] == '/') {
            return false;
        }

        /*if (strpos($d, '--') !== false) {
            return false;
        }*/

        return true;
    }

    public function __construct(
        SslJobRepository $sslJobRepository,
        LocationRepository $locationRepository
    )
    {
        $this->locationRepository = $locationRepository;
        $this->sslJobRepository = $sslJobRepository;
        $this->clip = SslLoggingClipFactory::globalClip();
    }

    /**
     * repariert die Domain eines Unternehmens
     * @param LogFacade $fac
     * @param Location $loc
     * @return bool - TRUE, wenn Domain gültig ist
     */
    public function repairLocationDomain(LogFacade $fac, Location $loc)
    {

        $d = $loc->domain;
        $id = $loc->id;

        $save = false;

        if (!self::isDomainValid($d)) {
            $fac->error("job $id: domain $d is invalid");
            $d = self::repairDomainOnly($d);
            if (self::isDomainValid($d)) {
                $loc->domain = $d;
                $fac->info("repaired");
                return true;
            } else {
                $fac->error("could not be repaired. deactivating");
                $loc->status_cert_gen = SslJobStatus::STATUS_DEACTIVATED;
                return false;
            }
        }

        return true;
    }

    /**
     * repariert die Ftp-Daten eines Unternehmens
     * @param LogFacade $fac
     * @param Location $loc
     * @return bool - TRUE, wann geändert wurde
     */
    public function repairLocationFtpData(LogFacade $fac, Location $loc)
    {

        $id = $loc->id;

        $hash1 = $loc->hashOfFtpData();
        $loc->ftpdirectoryhtml = trim($loc->ftpdirectoryhtml);
        $loc->ftphost = trim($loc->ftphost);
        $loc->ftppassword = trim($loc->ftppassword);
        $loc->ftpusername = trim($loc->ftpusername);

        if ($loc->ftpdirectoryhtml[0] != '/') {
            $loc->ftpdirectoryhtml = '/' . $loc->ftpdirectoryhtml;
        }

        $hash2 = $loc->hashOfFtpData();
        if ($hash1 != $hash2) {
            $fac->info("location $id: reparing ftp <$hash1> to ftp <$hash2>");
            return true;
        }

        return false;
    }

    public function restartWholeSslForLocationDomain($domain)
    {
        $loc = $this->sslJobRepository->findLocationByDomain($domain);
        if ($loc == null) {
            return false;
        }
        return $this->restartWholeSslForLocation($loc);
    }

    public function restartWholeSslForLocationId($id)
    {
        $loc = $this->sslJobRepository->byId($id);
        if ($loc == null) {
            return false;
        }
        return $this->restartWholeSslForLocation($loc);
    }

    /**
     * adds additional ssl information to location
     * same location is returned
     *
     * @param Location $loc
     * @return Location
     */
    public function addAdditionalSslInfoToLocation(Location $loc)
    {
        $info = new LocationSslInfo();

        $domain = $loc->domain;
        if (!StringUtils::isTooShort($domain, 3)) {
            $path = $this->getPathOfPrivateKeyFile($domain);
            if (file_exists($path)) {
                $mtime = filemtime($path);
                $info->private_key_file_modification_datetime = date('Y-m-d H:i:s', $mtime);
            }
        }

        $loc->ssl_info = $info;
        return $loc;
    }

    /**
     * @param Location $loc
     * @return CheckBuilder
     */
    public function checkLocation(Location $loc)
    {
        $domain = $loc->domain;

        $fines1 = strtotime('-2 months');
        $fines2 = strtotime('-3 months');
        $certFile = $this->getPathOfPrivateKeyFile($domain);
        $certFileExists = file_exists($certFile);
        $certFileTime = $certFileExists ? filemtime($certFile) : -1;

        $c = CheckBuilder::create();
        $c
            ->field('ssl_active')
                ->error('SSL muss aktiviert werden',$loc->ssl_active != 1)
            ->field('ssl_origin')
                ->error('SSL muss von uns gehostet werden',$loc->ssl_origin != 0)
            ->field('ftp_credentials_checked')
                ->error('FTP-Daten ungültig. Bitte bearbeiten', $loc->ftp_credentials_checked != 1)
            ->field('status_cert_gen')
                ->error('permanent deaktiviert wegen vorheriger Kündigung. Anstoßen des kompletten SSL-Prozess empfohlen', $loc->status_cert_gen == SslJobStatus::STATUS_DEACTIVATED_FOREVER)
                ->warn('Generierungsfehler. Auftrag im Wiederanlauf. Beobachtung empfohlen', $loc->status_cert_gen == SslJobStatus::STATUS_ERROR)
            ->field('private_key_file_modification_datetime')
                ->error('privater Schlüssel des Zertifikats existiert nicht. Kompletter Prozess empfohlen', !$certFileExists)
                ->warn('Zertifikatsdatei schon älter.', $certFileExists && $certFileTime < $fines1)
                ->error('Zertifikatsdatei zu alt und vermutlich abgelaufen', $certFileExists && $certFileTime < $fines2)
        ;

        $loc->ssl_check_bucket = $c->toArray();
        return $c;
    }

    public function restartWholeSslForLocation(Location $loc)
    {
        $loc->status_cert_gen = SslJobStatus::STATUS_NEW;
        $loc->status_cert_import = SslJobStatus::STATUS_NEW;
        $loc->ssl_options = '[overwrite]';
        $loc->save();
        return true;
    }

    public function forceImportById($id)
    {
        $loc = $this->sslJobRepository->byId($id);
        if ($loc == null) {
            return false;
        }
        return $this->forceImportOfLocation($loc);
    }

    public function forceImport(LogFacade $fac, $domain)
    {
        $loc = $this->sslJobRepository->findLocationByDomain($domain);

        if ($loc == null) {
            $fac->error("imported force for domain $domain but it was not found in system");
            return false;
        }

        $this->forceImportOfLocation($loc);
        $fac->info("domain $domain forced for reimport");
        return true;
    }

    private function forceImportOfLocation(Location $loc)
    {
        $loc->status_cert_import = SslJobStatus::STATUS_NEW;
        $loc->ssl_options = '[overwrite]';
        $loc->save();
        return true;
    }

    public function redoNeededCertGeneration()
    {
        return $this->sslJobRepository->redoNeededCertGeneration();
    }

    public function processSingleJob(CompositeFacade $fac, $id)
    {
        $job = $this->sslJobRepository->byId($id);
        if ($job === null) {
            $fac->error("job with id $id not found");
            return;
        }

        $fac->withAt($this->jobLogger($job), 0);

        $this->processJob($fac, $job);
    }

    public function processNotFinishedJobs(CompositeFacade $fac)
    {
        $fac->withAt($this->clip, 1);
        $fac->info('starting processOpenJobs in SslService');

        $fac->info('load new jobs');
        $jobs = $this->sslJobRepository->findNewJobs();
        $fac->info('jobs loaded');
        $this->processJobs($fac, $jobs);

        $fac->info('load error jobs');
        $jobs = $this->sslJobRepository->findErrorJobs();
        $fac->info('jobs loaded');
        $this->processJobs($fac, $jobs);
    }

    /**
     * @param CompositeFacade $fac
     * @param Location[] $jobs
     */
    public function processJobs(CompositeFacade $fac, $jobs)
    {
        foreach ($jobs as $job) {

            try {
                $c = SslLoggingClipFactory::forJob($job);
                $fac->info('processing job with id and domain ' . $job->id . ' ' . $job->domain);
                $fac->withAt($c, 0);
                $this->processJob($fac, $job);
                $fac->info('done successful');
            } catch (\Exception $e) {
                $fac->error("error processing job {$job->id}");
                $fac->exception($e);
            }
        }
    }

    public function processJob(LogFacade $fac, Location $job)
    {
        $this->processJobCreateCertificate($fac, $job);
    }

    /**
     * @param $filter
     * @return SslJob[]
     */
    public function findJobsAndTheirLocationsSimple($filter)
    {
        return $this->sslJobRepository->findJobs($filter, true);
    }

    /**
     * @param $filter
     * @param bool $loadLocations
     * @param null $orderBy
     * @return Location[]
     */
    public function findJobs($filter, $orderBy = null, $columns = null)
    {
        return $this->sslJobRepository->findJobs($filter, $orderBy, $columns);
    }

    /**
     * returns amount of matching companies
     *
     * @param $filter
     * @return int
     */
    public function countJobs($filter = [])
    {
        return $this->sslJobRepository->countJobs($filter);
    }

    /**
     * @param $id
     * @return Location|null
     */
    public function findByIdId($id)
    {
        return $this->sslJobRepository->byId($id);
    }

    public function getPathOfPrivateKeyFile($domain)
    {
        $te = QuickTemplateEngine::create()->withValue('domain', $domain);
        return $te->applyToString(self::TEMPLATE_PATH_PRIVATEKEY);
    }

    public function getPathOfFullChainFile($domain)
    {
        $te = QuickTemplateEngine::create()->withValue('domain', $domain);
        return $te->applyToString(self::TEMPLATE_PATH_FULLCHAIN);
    }

    public function getContentOfPrivateKeyFile($domain)
    {
        return FileUtils::contentOfFileOrNull($this->getPathOfPrivateKeyFile($domain));
    }

    public function getContentOfFullChainFile($domain)
    {
        return FileUtils::contentOfFileOrNull($this->getPathOfFullChainFile($domain));
    }

    public function addFileContentsToJob(Location $job)
    {
        $job->privatekeyfilecontent = $this->getContentOfPrivateKeyFile($job->domain);
        $job->fullchainfilecontent = $this->getContentOfFullChainFile($job->domain);
    }

    private function processJobCreateCertificate(LogFacade $fac, Location $loc)
    {
        $fac->info('processJobCreateCertificate');
        $lastErrors = [];

        $success = true;

        $validDomain = $this->repairLocationDomain($fac, $loc);
        if (!$validDomain) {
            $fac->error("domain is invalid $loc->domain");
            $loc->last_ssl_error_message = "domain invalid: $loc->domain";
            $loc->last_cert_gen_touched = DateTimeUtils::nowAsString();
            $loc->status_cert_gen = SslJobStatus::STATUS_ERROR;
            $loc->ssl_count_processed_gen++;
            $loc->save();
            return;
        }

        $hookFileName = FileUtils::tmpFile("ssl-hook-{$loc->id}.sh");
        $mainFileName = FileUtils::tmpFile("ssl-generate-{$loc->id}.sh");
        $shellOutputFileName = FileUtils::tmpFile("output-{$loc->id}.txt");
        $fac->info("hookFileName: $hookFileName");
        $fac->info("mainFileName: $mainFileName");

        $arr = [
            'domain' => $loc->domain,
            'ftpdirectoryhtml' => $loc->ftpdirectoryhtml,
            'ftphost' => $loc->ftphost,
            'ftppassword' => $loc->ftppassword,
            'ftpusername' => $loc->ftpusername,
            'hook' => $hookFileName,
            'mainoutputfile' => $shellOutputFileName
        ];

        $fac->info("template data: " . StringUtils::arrayToString($arr));
        $te = QuickTemplateEngine::create()->withValues($arr);

        //generate the hook
        $hookContent = FileUtils::contentOfPackageFile($this, 'hook.template.sh');
        $te->applyToString($hookContent);
        FileUtils::dumpStringToFile($te->applyToString($hookContent), $hookFileName);
        chmod($hookFileName, 0777);

        //generate the script
        $mainContent = FileUtils::contentOfPackageFile($this, 'generate-cert.template.sh');
        $te->applyToString($mainContent);
        FileUtils::dumpStringToFile($te->applyToString($mainContent), $mainFileName);
        chmod($mainFileName, 0777);

        //execute script
        $cmd = "$mainFileName";
        system($cmd);
        $fac->info("commandExecuted: $cmd");

        $fileCreatedNow = false;

        //check if cert is there
        $filename = $te->applyToString(self::TEMPLATE_PATH_PRIVATEKEY);
        $fac->info("checking if filename $filename exists");
        if (!file_exists($filename)) {
            $fac->error("file not present $filename");
            $lastErrors[] = "file not present $filename";
            $success = false;
        } else {
            //check is not too old
            $mtime = filemtime($filename);
            $fac->info('file has a modifcation time of ' . date('Y-m-d H:i:s', $mtime));
            $fines = strtotime('+80 days', $mtime);
            $fines2 = strtotime('-1 day');
            $now = time();

            if ($fines <= $now) {
                $fac->error("file is present but too old $filename " . date("Y-m-d", $mtime));
                $lastErrors[] = "file is present but too old $filename " . date("Y-m-d", $mtime);
                $success = false;
            }

            if ($mtime >= $fines2) {
                $fac->info("modification is newly created. newly means before " . date('Y-m-d H:i:s', $fines2));
                $fac->info('so we have a new file and this needs to be imported');
                $fileCreatedNow = true;
            }

        }

        $filename = $te->applyToString(self::TEMPLATE_PATH_FULLCHAIN);
        $fac->info("checking if filename $filename exists");
        if (!file_exists($filename)) {
            $fac->error("file not present $filename");
            $lastErrors[] = "file not present $filename";
            $success = false;
        }

        $now = DateTimeUtils::nowAsString();

        $loc->last_cert_gen_touched = $now;

        if ($success) {
            $loc->status_cert_gen = SslJobStatus::STATUS_SUCCESS;
            $loc->last_cert_gen = $now;
        } else {
            $loc->last_ssl_error = $now;
            $loc->last_ssl_error_message = implode("\n", $lastErrors);
            $loc->status_cert_gen = SslJobStatus::STATUS_ERROR;
        }
        $loc->ssl_count_processed_gen++;

        if ($success) {
            $fac->info("success - force import of new cert");
            $loc->status_cert_import = SslJobStatus::STATUS_NEW;
            $loc->ssl_options = '[overwrite]';
        }

        $loc->save();

        $fac->info("processing job {$loc->id} done");
    }

    public function removeDuplicateJobs()
    {
        $q = 'select count(*),domain,name from ssljobs group by domain,name having count(*)>1';
        $list = DB::select($q);
        $sum = 0;
        foreach ($list as $obj) {
            $domain = $obj->domain;
            $name = $obj->name;
            $sum += $this->removeDuplicatesOfJob($domain, $name);
        }
        return $sum;
    }

    public function removeDuplicatesOfJob($domain, $name)
    {
        $sum = 0;
        $q = 'select id from ssljobs where domain=? and name=? order by id asc';
        $list = DB::select($q, [$domain, $name]);
        $i = 0;
        foreach ($list as $job) {
            $id = $job->id;
            if ($i++ == 0) {
                echo "ignore $id\n";
                continue;
            }
            $q = "delete from ssljobs where id=$id";
            echo $q . "\n";
            DB::update($q);
            $sum++;
        }
        return $sum;
    }

    /**
     * @param Location $job
     * @return LoggingClip
     */
    private function jobLogger(Location $job)
    {
        return SslLoggingClipFactory::forJob($job);
    }

    private function repairDomain(Location $loc)
    {
        $d = $loc->domain;
        $repaired = self::repairDomainOnly($d);
        if (self::isDomainValid($repaired)) {
            $loc->domain = $d;
            return true;
        }
        return false;
    }

    public static function repairDomainOnly($d)
    {
        if ($d !== null && strlen($d) > 0) {
            $d = trim($d);
            $d = str_replace(['http://', 'https://', 'http://www.', 'https://www.'], '', $d);
            if (strpos($d, 'www.') === 0) $d = substr($d, 4);
            if ($d[strlen($d) - 1] == '/') $d = substr($d, 0, -1);
            return $d;
        }
    }


}
