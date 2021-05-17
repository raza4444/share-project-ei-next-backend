<?php

/**
 * by stephan scheide
 */

namespace App\Console\Commands;


use App\Entities\Ssl\SslJob;
use App\Entities\Ssl\SslJobStatus;
use App\Entities\Ssl\SslVerifyResult;
use App\Services\Ssl\SslService;
use App\Services\Ssl\SslVerifyResultService;
use Illuminate\Console\Command;

class SslCertVerifyCommand extends Command
{
    const ERROR_LIMIT = 1000;

    protected $name = 'application:ssl-cert-verify';

    private $sslService;

    private $sslVerifyResultService;

    public function __construct(
        SslService $sslService,
        SslVerifyResultService $sslVerifyResultService
    ) {
        parent::__construct();
        $this->sslService = $sslService;
        $this->sslVerifyResultService = $sslVerifyResultService;
    }

    public function handle()
    {
        $this->sslVerifyResultService->clear();

        $limitError = self::ERROR_LIMIT;
        $invalidDomains = [];
        $expiredDomains = [];

        $countSuc = 0;
        $countErr = 0;
        $current = 0;

        $filter = [
            'ssl_active' => true
        ];
        $jobs = $this->sslService->findJobs($filter);
        $countTotal = count($jobs);

        foreach ($jobs as $job) {

            $vr = SslVerifyResult::createNew();

            $current++;
            $this->info("handle $current/$countTotal");
            $domain = $job->domain;
            $vr->domain = $domain;

            $outputArray = $this->getRawCertInfo($domain);
            $output = $outputArray[0];
            $outputRaw = $outputArray[1];

            $vr->rawoutput = $outputRaw;
            $vr->details = '';

            if ($job->status_cert_gen == SslJobStatus::STATUS_DEACTIVATED_FOREVER) {
                $vr->details .= "Dieser Job ist für immer deaktiviert. Der Kunde hatte einmal gekündigt. Daher ist es OK, wenn die Zertifikate nicht mehr erzeugt werden.";
            }

            if ($output == null) {
                $this->error("err: invalid: $domain - cause: output is null");
                $vr->details .= "Konnte das Zertifikat nicht abrufen. Das ist ungewöhnlich. Ist es eine valide Domain? Ist der Webserver online? Stimmt der DNS? Sind Leerzeichen vor oder hinter dem Namen? (" . $domain . ")";
                if (trim($domain) != $domain) {
                    $vr->details .= "Es scheinen tatsächlich Leerzeichen oder andere nicht sichtbare Zeichen da zu sein. Bitte korrigieren und der Entwicklung Bescheid geben.";
                }

                $countErr++;
                $invalidDomains[] = $domain;
            } else {

                $vr->valid = $output[0];
                $vr->dateuntil = $output[3];
                $vr->certissuer = $output[2];
                $vr->validdateuntil = $output[4];
                $vr->validissuer = $output[5];

                $str = implode(";", $output);
                if ($output[0] == 1) {
                    $this->info("suc: valid: $domain - output: $str");
                    $vr->details .= "Zertifikat funktioniert";
                    $countSuc++;
                } else {
                    $this->error("err: invalid: $domain - output: $str");
                    $invalidDomains[] = $domain;
                    $countErr++;

                    if ($output[5] == 1) {
                        //issuer is valid, but date not --> ours, expired
                        if ($output[4] == 0) {
                            $this->error("expired $domain");
                            $expiredDomains[] = $domain;
                            $vr->details .= "Zertifikat ist von Let's Encrypt und damit von uns erstellt, ist aber abgelaufen";
                            $vr->expired = 1;
                        } else {
                            $vr->details .= "Interner Fehler"; //kann nicht vorkommen
                        }
                    } else {
                        $vr->details .= "Zertifikat ist nicht von uns. Kann bei fremdgehosteten Kunden richtig sein.";
                        if ($output[4] == 0) {
                            $vr->details .= "Und es ist auch abgelaufen";
                        }
                    }
                }
            }

            $this->sslVerifyResultService->saveDomainResult($vr);

            if ($countErr > $limitError) {
                $this->error("err: limit of $limitError reached");
                break;
            }
        }
        $this->error("show invalid domains");
        foreach ($invalidDomains as $d) {
            echo $d . "\n";
        }
        $this->error("show expired domains " . count($expiredDomains));
        foreach ($expiredDomains as $d) {
            echo $d . "\n";
        }
        $this->info("success $countSuc errors $countErr total $countTotal");

        return 0;
    }

    private function getRawCertInfo($domain)
    {
        $cmd = "echo | openssl s_client -servername $domain -connect $domain:443 2>/dev/null | openssl x509 -noout -dates -issuer";
        $output = shell_exec($cmd);
        $parsed = self::extractOutputData($output);
        return [$parsed, $output];
    }

    /*
     * returns array if checkable or null
     * indices
     * 0: overall valid state
     * 1: date value
     * 2: issuer
     * 3: date formatted
     * 4: 1 if time is valid (expire time of cert)
     * 5: 1 if issuer is valid (lets encrypt)
     *
     */
    public static function extractOutputData($output)
    {
        $lines = explode("\n", strtolower($output));

        if (count($lines) < 3) {
            return null;
        }
        if (strlen($lines[1]) < 11) {
            return null;
        }
        if (strlen($lines[2]) < 8) {
            return null;
        }

        $months = [
            'jan' => 1,
            'feb' => 2,
            'mar' => 3,
            'apr' => 4,
            'may' => 5,
            'jun' => 6,
            'jul' => 7,
            'aug' => 8,
            'sep' => 9,
            'oct' => 10,
            'nov' => 11,
            'dec' => 12
        ];

        $issuer = substr($lines[2], 7);
        $date = explode(' ', str_replace("  ", " ", substr($lines[1], 9)));
        $month = $date[0];
        $day = $date[1];
        $year = $date[3];

        if (!array_key_exists($month, $months)) {
            return null;
        }

        $month = $months[$month];
        $now = time();
        //print_r($date);
        $dateValue = mktime(0, 0, 0, $month, $day, $year);
        $dateVisible = date('Y-m-d', $dateValue);

        $validTime = $now < $dateValue;
        $validIssuer = strpos($issuer, 'let\'s encrypt') !== false;
        $valid = $validTime && $validIssuer;

        $result = [
            $valid ? 1 : 0,
            $dateValue,
            $issuer,
            $dateVisible,
            $validTime ? 1 : 0,
            $validIssuer ? 1 : 0
        ];
        return $result;
    }
}
