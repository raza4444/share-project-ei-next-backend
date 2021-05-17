<?php
/**
 * by stephan scheide
 */

namespace App\Servers\Tasks;


use App\Entities\Tasks\CounterTaskEvent;
use App\Logging\QuickLog;
use App\Repositories\Tasks\CounterTaskEventRepository;
use App\Servers\SingleServer;

class TaskReservationServer extends SingleServer
{

    private const SECONDS_UNTIL_RESET = 600;

    public $port = 10001;

    private $counterTaskEventRepository;

    private $eventManager;

    private $time = 0;

    public function __construct(CounterTaskEventRepository $counterTaskEventRepository)
    {
        $this->counterTaskEventRepository = $counterTaskEventRepository;
        $this->eventManager = new EventManager();
    }

    /**
     * accept loop of the server
     */
    public function accept()
    {


        $sock = $this->sock;

        $this->debug('server accepting clients now');

        do {
            if (($msgsock = socket_accept($sock)) === false) {
                echo "socket_accept() fehlgeschlagen: Grund: " . socket_strerror(socket_last_error($sock)) . "\n";
                break;
            }

            // Neuer Client kommt
            $this->debug('*** accepting new client');
            $this->loadOpenTasks();

            do {
                if (false === ($buf = socket_read($msgsock, 2048, PHP_NORMAL_READ))) {
                    echo "socket_read() fehlgeschlagen: Grund: " . socket_strerror(socket_last_error($msgsock)) . "\n";
                    break 2;
                }
                if (!$buf = trim($buf)) {
                    continue;
                }

                $this->debug('got command: ' . $buf);
                $response = 'unknown-command';
                $doBreak = false;

                if ($buf == 'reload') {
                    $this->markForReloadOfOpenTasks();
                    $response = 'ok-reload';
                    $doBreak = true;
                } else if ($buf == 'quit') {
                    break;
                } else if (strpos($buf, 'consume-') === 0) {

                    $tmp = explode('-', $buf);
                    $counterTaskId = $tmp[1];
                    $userId = $tmp[2];

                    $eventId = $this->findNextOpenTaskFor($counterTaskId, $userId);
                    if ($eventId === null) $eventId = 0;
                    $response = "ok-$eventId";
                    $doBreak = true;

                } else if (strpos($buf, 'count-') === 0) {

                    $tmp = explode('-', $buf);
                    $counterTaskId = $tmp[1];
                    $userId = $tmp[2];

                    $cc = $this->countOpenTasks($counterTaskId, $userId);
                    if ($cc === null) $cc = 0;
                    $response = "ok-$cc";
                    $doBreak = true;

                }
                $this->debug("sending response $response");

                socket_write($msgsock, $response, strlen($response));

                if ($doBreak) break;

            } while (true);
            socket_close($msgsock);
        } while (true);
    }

    private function countOpenTasks($counterTaskId, $userId)
    {
        return $this->eventManager->countOpenByCounterTaskId($counterTaskId);
    }

    private function loadOpenTasks()
    {
        $currentTime = time();
        $delta = $currentTime - $this->time;
        if ($this->time > 0 && $delta > self::SECONDS_UNTIL_RESET) {
            $this->time = $currentTime;
            $this->debug('clearing event manager due to delta ' . $delta . ' seconds');
            $this->eventManager->clear();
        }

        $events = $this->counterTaskEventRepository->findAllOpen();
        $this->debug('open tasks loaded from backend ' . count($events));
        foreach ($events as $event) {
            $this->debug('adding event ' . $event->id);
            $presentEvent = $this->eventManager->eventById($event->id);
            if ($presentEvent === null) {
                $this->eventManager->addEvent($event);
            }
        }
    }

    private function markForReloadOfOpenTasks()
    {
    }

    private function findNextOpenTaskFor($counterTaskId, $userId)
    {
        return $this->eventManager->nextOpenEventIdFor($counterTaskId, $userId);
    }

    /**
     * overwrites debug and logs to output and file
     * @param $any
     */
    protected function debug($any)
    {
        QuickLog::quickWithName('task-reservation-server');
        parent::debug($any);
    }


}