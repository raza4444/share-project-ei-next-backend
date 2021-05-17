<?php

namespace App\Console\Commands;

use App\Entities\ContactFormAutomation\CFALogs;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteCFALogsAfterEveryEightHoursCommand extends Command
{
    protected $name = 'application:report-crawler-results';

    public function handle() {
        CFALogs::where('created_at', '<', Carbon::now()->subHours(48)->delete());
    }

}
