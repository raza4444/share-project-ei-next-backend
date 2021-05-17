<?php
/**
 * by stephan scheide
 */

namespace App\Console\Commands;


use App\Services\Ssl\SslService;
use Illuminate\Console\Command;

class SslCleanCommand extends Command
{
    protected $name = 'application:ssl-clean';

    private $sslService;

    public function __construct(SslService $sslService)
    {
        parent::__construct();
        $this->sslService = $sslService;
    }

    public function handle()
    {
        $cc = $this->sslService->removeDuplicateJobs();
        $this->info("duplicate jobs removed: $cc");
        return 0;
    }


}
