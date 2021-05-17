<?php
/**
 * by stephan scheide
 */

namespace App\Console\Commands;

set_time_limit(0);

ob_implicit_flush();

use App\Repositories\Tasks\CounterTaskEventRepository;
use App\Servers\Tasks\TaskReservationServer;
use Illuminate\Console\Command;

class ServeTaskReservationCommand extends Command
{

    protected $description = 'starts the tcp/ip-service which handles reservation of tasks';

    protected $name = 'application:serve-task-reservation';

    private $counterTaskEventRepository;

    public function __construct(
        CounterTaskEventRepository $counterTaskEventRepository
    )
    {
        parent::__construct();
        $this->counterTaskEventRepository = $counterTaskEventRepository;
    }

    public function handle()
    {

        $server = new TaskReservationServer($this->counterTaskEventRepository);
        echo "semaphore single locking server for tasks listening at port " . $server->port . "\n";
        $server->run();
        echo "server done";
        return 0;
    }

}
