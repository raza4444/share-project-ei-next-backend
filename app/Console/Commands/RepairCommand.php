<?php
/**
 * by stephan scheide
 */

namespace App\Console\Commands;


use App\Services\Repair\RepairService;
use Illuminate\Console\Command;

class RepairCommand extends Command
{

    protected $name = 'application:repair';

    public function handle(RepairService $repairService)
    {
        $repairService->repair();
    }

}
