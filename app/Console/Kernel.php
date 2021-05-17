<?php

namespace App\Console;

use App\Console\Commands\DbTransformCommand;
use App\Console\Commands\DeleteAppointmentsOfAdmin;
use App\Console\Commands\RepairCommand;
use App\Console\Commands\ReportCrawlerResults;
use App\Console\Commands\ServeEventReservationCommand;
use App\Console\Commands\ServeTaskReservationCommand;
use App\Console\Commands\SslCertVerifyCommand;
use App\Console\Commands\SslCleanCommand;
use App\Console\Commands\SslCommand;
use App\Console\Commands\CrawlerProcessStatusCommand;
use App\Console\Commands\GoogleGeoCodingCommand;
use App\Console\Commands\SslFileVerifyCommand;
use App\Console\Commands\ReadMailboxCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RepairCommand::class,
        DbTransformCommand::class,
        DeleteAppointmentsOfAdmin::class,
        ServeEventReservationCommand::class,
        ServeTaskReservationCommand::class,
        SslCommand::class,
        SslCertVerifyCommand::class,
        SslFileVerifyCommand::class,
        SslCleanCommand::class,
        ReportCrawlerResults::class,
        CrawlerProcessStatusCommand::class,
        GoogleGeoCodingCommand::class,
        ReadMailboxCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('application:report-crawler-results')->everyMinute();
        // $schedule->command('application:report-crawler-results')->cron('0 */8 * * *');
    }
}
