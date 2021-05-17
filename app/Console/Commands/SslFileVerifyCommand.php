<?php
/**
 * by stephan scheide
 */

namespace App\Console\Commands;


use App\Services\Ssl\SslService;
use Illuminate\Console\Command;

class SslFileVerifyCommand extends Command
{

    protected $name = 'application:ssl-file-verify';

    private $sslService;

    public function __construct(SslService $sslService)
    {
        parent::__construct();
        $this->sslService = $sslService;
    }

    public function handle()
    {
        $dir = SslService::LETS_ENCRYPT_PATH.'/live';
        $h = opendir($dir);
        while ($entry = readdir($h)) {
            if (strlen($entry) == 0 || $entry[0] == '.') {
                continue;
            }
            $full = $dir . '/' . $entry;
            if (is_dir($full)) {
                $this->checkDomainDir($full, $entry);
            }
        }

        return 0;
    }

    /**
     * @param $full
     * @param $relative
     */
    private function checkDomainDir($full, $relative)
    {
        $domain = $relative;
        $cert = $full . '/cert.pem';
        $now = time();
        if (file_exists($cert)) {
            $mtime = filemtime($cert);
            $fines = strtotime('+90 days', $mtime);
            if ($fines > $now) {
                $this->info(date('Y-m-d', $mtime) . ' ' . $domain . ' valid till ' . date('Y-m-d', $fines));
            } else {
                $this->error('ERR: '.date('Y-m-d', $mtime) . ' ' . $domain . ' valid till ' . date('Y-m-d', $fines));
            }
        } else {
            $this->error("ERR: file not found $cert");
        }
    }
}
