<?php
/**
 * by stephan scheide
 */

namespace App\Console\Commands;

set_time_limit(0);

ob_implicit_flush();

use App\Servers\EventReservationServer;
use App\Services\Branches\LocationEventsToBeDoneService;
use App\Services\Core\UserAbsenceService;
use Illuminate\Console\Command;

class ServeEventReservationCommand extends Command
{

    protected $description = 'starts the tcp/ip-service which handles reservation of school_events';

    protected $name = 'application:serve-event-reservation';

    private $locationEventsToBeDoneService;

    private $userAbsenceService;

    public function __construct(
        LocationEventsToBeDoneService $locationEventsToBeDoneService,
        UserAbsenceService $userAbsenceService
    )
    {
        parent::__construct();
        $this->locationEventsToBeDoneService = $locationEventsToBeDoneService;
        $this->userAbsenceService = $userAbsenceService;
    }

    public function handle()
    {
        $server = new EventReservationServer($this->userAbsenceService, $this->locationEventsToBeDoneService);
        echo "semaphore single locking server listening at port " . $server->port . "\n";
        $server->run();
        echo "server done";
        return 0;
    }

}
