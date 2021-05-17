<?php
/**
 * by stephan scheide
 */

namespace App\Console\Commands;


use App\Entities\Ssl\SslJob;
use App\Entities\Ssl\SslJobStatus;
use App\Logging\BlackHoleLogFacade;
use App\Logging\CommandLogFacade;
use App\Logging\CompositeFacade;
use App\Repositories\Ssl\SslJobRepository;
use App\Services\Ssl\SslLoggingClipFactory;
use App\Services\Ssl\SslService;
use App\Utils\FileUtils;
use Illuminate\Console\Command;

class SslCommand extends Command
{

    protected $name = 'application:ssl';

    protected $signature = 'application:ssl {operation} {--jobid=} {--domain=}';

    private $sslService;

    private $sslJobRepository;

    public function __construct(SslService $sslService, SslJobRepository $sslJobRepository)
    {
        parent::__construct();
        $this->sslService = $sslService;
        $this->sslJobRepository = $sslJobRepository;
    }

    public function handle()
    {
        $this->info('ssl command line util');

        $op = $this->argument('operation');

        if ($op === 'process-jobs') {
            return $this->processJobs();
        } else if ($op === 'test') {
            return $this->test();
        } else if ($op == 'process-single-job') {
            return $this->processSingleJob($this->option('jobid'));
        } else if ($op == 'redo-needed-cert-generation') {
            return $this->redoNeededCertGeneration();
        } else if ($op == 'force-import') {
            return $this->forceImport($this->option('domain'));
        } else if ($op == 'whole-restart') {
            return $this->wholeRestart($this->option('domain'));
        } else if ($op == 'query-domain') {
            return $this->queryDomain($this->option('domain'));
        } else if ($op == 'analyse-gen-errors') {
            return $this->analyseGenErrors();
        } else {
            $this->error('unknown operation ' . $op);
            $this->printHelp();
            return 1;
        }

        return 0;
    }

    private static function has($haystack, $needle)
    {
        return strpos($haystack, $needle) !== false;
    }

    private function analyseGenErrors()
    {
        $filter = ['status_cert_gen' => SslJobStatus::STATUS_ERROR];

        $jobs = $this->sslService->findJobs($filter);
        $cc = count($jobs);
        $counts = [
            'unknown' => 0,
            'doctype' => 0,
            'renew' => 0,
            'dns' => 0, 'nooutput' => 0, 'noascii' => 0, 'noauth' => 0, 'empty' => 0, 'web403' => 0, 'web406' => 0, 'web204' => 0, 'web404' => 0];
        foreach ($jobs as $j) {
            $reason = [];
            try {
                $outputFilePath = "/tmp/output-" . $j->id . '.txt';
                $output = FileUtils::contentOfFileOrNull($outputFilePath);
                if ($output === null) {
                    $reason[] = "Ausgabedatei $outputFilePath nicht gefunden";
                    $counts['nooutput']++;
                } else {
                    $output = strtolower($output);
                    if (self::has($output, 'non-ascii')) {
                        $counts['noascii']++;
                        $reason[] = 'umlaut';
                    }
                    if (self::has($output, 'unauthorized')) {
                        $counts['noauth']++;
                        $reason[] = 'unauth';
                    }
                    if (strlen($output) == 0) {
                        $counts['empty']++;
                        $reason[] = 'empty';
                    }
                    if (self::has($output, ": 403\n")) {
                        $counts['web403']++;
                        $reason[] = '403 vom Webserver';
                    }
                    if (self::has($output, ": 404\n")) {
                        $counts['web404']++;
                        $reason[] = '404 vom Webserver';
                    }
                    if (self::has($output, ": 406\n")) {
                        $counts['web406']++;
                        $reason[] = '406 vom Webserver';
                    }
                    if (self::has($output, ": 204\n")) {
                        $counts['web204']++;
                        $reason[] = '204 vom Webserver';
                    }
                    if (self::has($output, 'dns problem:')) {
                        $counts['dns']++;
                        $reason[] = 'DNS Problem.';
                    }
                    if (self::has($output, '<!doctype') || self::has($output, '<html')) {
                        $counts['doctype']++;
                        $reason[] = 'Webserver liefert Fehlerseite statt Challenge-Datei';
                    }
                    if (self::has($output, 'cert not yet due for renewal')) {
                        $counts['renew']++;
                        $reason[] = 'Wollte erneut generieren, obwohl noch aktuell';
                    }
                }
            } catch (\Exception $e) {
                $reason[] = 'Fehler bei Analyse';
            }

            if (count($reason) == 0) {
                $reason[] = 'unbekannt';
                $counts['unknown']++;
            }

            $line = "job;" . $j->id . ";" . $j->domain . ";" . $j->lasterror . ';reason;' . implode(";", $reason);
            $this->info($line);
        }

        $this->info('');
        foreach ($counts as $k => $v) {
            $this->info("Anzahl $k: $v");
        }

        $this->info("total count failed generations: $cc");
    }

    private function queryDomain($domain)
    {
        $filter = ['domain' => $domain];
        $jobs = $this->sslService->findJobs($filter, true, 'id asc');
        $this->info($jobs);
        $this->info('');

        foreach ($jobs as $j) {
            $arr = $j->attributesToArray();
            ksort($arr);
            foreach ($arr as $k => $v) {
                $this->info("$k: $v");
            }
            $this->info('');

        }
        $this->info('');

        foreach ($jobs as $j) {
            $line = "Job " . $j->id . " " . $j->name . " " . $j->status . " " . $j->domain;
            $this->info($line);
        }
    }

    private function forceImport($domain)
    {
        $fac = $this->defaultClipFacade();
        $this->sslService->forceImport($fac, $domain);
    }

    private function wholeRestart($domain)
    {
        $fac = $this->defaultClipFacade();
        $fac->info('starting restart for ' . $domain);
        if (!$this->sslService->restartWholeSslForLocationDomain($domain)) {
            $fac->error('could not process domain '.$domain.' - maybe it does not exist');
        }
        $fac->info('done');
    }

    private function redoNeededCertGeneration()
    {
        $fac = $this->defaultClipFacade();
        $fac->info('mark jobs with old certificates as new');
        $this->sslService->redoNeededCertGeneration();
    }

    private function test()
    {
        $this->info("showing random error jobs");
        $jobs = $this->sslJobRepository->findErrorJobs();
        foreach ($jobs as $j) {
            $this->info("jobid {$j->id}");
        }
    }

    private function processSingleJob($id)
    {
        $fac = $this->defaultClipFacade();
        $this->sslService->processSingleJob($fac, $id);
    }

    private function processJobs()
    {

        $fac = $this->defaultClipFacade();

        $this->info('processing job');
        $this->sslService->processNotFinishedJobs($fac);
    }

    private function defaultSslClipFacade()
    {
        $fac = CompositeFacade::create()->withMany(
            BlackHoleLogFacade::createNew(),
            SslLoggingClipFactory::globalClip(),
            CommandLogFacade::createNew($this)
        );
        return $fac;
    }

    private function defaultClipFacade()
    {
        $fac = CompositeFacade::create()->withMany(
            BlackHoleLogFacade::createNew(),
            BlackHoleLogFacade::createNew(),
            CommandLogFacade::createNew($this)
        );
        return $fac;
    }

    private function printHelp()
    {
        $this->info('<operation> with operation:');
        $this->info('analyse-gen-errors', 'analysis generation errors');
        $this->info('force-import: forces the importer to use the generated cert');
        $this->info('whole-restart: restarts whole ssl-process for given domain --domain=X');
        $this->info('process-jobs: process open jobs');
        $this->info('process-single-job: processes a job. state does not matter. --jobid=X');
        $this->info('redo-needed-cert-generation: marks jobs with too old certificates as new');
        $this->info('test: sometests');
        $this->info('query-domain: prints all info of domain X, maily jobs. --domain=X');
    }

}
