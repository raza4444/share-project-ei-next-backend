<?php
/**
 * by stephan scheide
 */

namespace App\Servers;

use App\Entities\Branches\CounterType;
use App\Logging\QuickLog;
use App\Services\Branches\LocationEventsToBeDoneService;
use App\Services\Branches\WorkEvent;
use App\Services\Core\UserAbsenceFacade;
use App\Services\Core\UserAbsenceService;
use Illuminate\Support\Facades\App;

/**
 * server which controls the reservation and locking of events
 *
 * Class EventReservationServer
 * @package Modules\Admin\Campaign\Servers
 */
class EventReservationServer extends SingleServer
{

    /**
     * Eine Abbildung
     * CounterType: int --> EventMap
     * EventMap: eventId: int --> EventData
     * EventData := Map{open:string,eventdata:WorkEvent}
     * @var array
     */
    private $events;

    private $locationEventsToBeDoneService;

    private $syncDecider;

    /**
     * @var UserAbsenceFacade
     */
    private $userAbsenceFacade;

    private $userAbsenceService;

    public function __construct(
        UserAbsenceService $userAbsenceService,
        LocationEventsToBeDoneService $locationEventsToBeDoneService
    )
    {
        $this->syncDecider = new SyncDecider();
        $this->events = [];
        $this->locationEventsToBeDoneService = $locationEventsToBeDoneService;
        $this->userAbsenceService = $userAbsenceService;
    }

    /**
     * registers event by id to server
     * if already present, nothing is done to keep the state of the event
     * @param $counterType int
     * @param $eventId int
     * @param $eventData \stdClass
     */
    public function registerEvent($counterType, $eventId, $eventData)
    {
        /**
         * @var WorkEvent $eventData ;
         */

        if (!array_key_exists($counterType, $this->events)) {
            $this->events[$counterType] = [];
        }
        if (array_key_exists($eventId, $this->events[$counterType])) {
            // $this->debug('already present - event ' . $eventId);
        } else {
            $this->events[$counterType][$eventId] = ['open' => true, 'eventdata' => $eventData];
        }
    }

    public function findFreeEventByCounterTypeAndLastUserId($counterType, $userId)
    {

        $this->debug("*** findFreeEventByCounterTypeAndLastUserId $counterType $userId");

        if ($this->userAbsenceFacade->isUserAbsent($userId)) {
            $this->debug("user $userId ist nicht anwesenheit --> Kein Ereignis.");
            return null;
        }

        if (!array_key_exists($counterType, $this->events)) return null;

        //Diese Schleife sucht nach einem Benutzerbezug
        foreach ($this->events[$counterType] as $eventId => $data) {

            /**
             * @var $eventData WorkEvent
             */
            $eventData = $data['eventdata'];

            if ($data['open']) {

                if ($eventData->wiedervorlage == 1 && $eventData->lastuserid == $userId) {
                    $this->debug('event ' . $eventId . ' hat Wiedervorlagenkennung - lastuserid: ' . $eventData->lastuserid);

                    $this->events[$counterType][$eventId]['open'] = false;
                    $this->debug('found event explicitly for user ' . $userId . ' => ' . $eventId);
                    return $eventId;
                }

            }
        }

        $this->debug("no wiedervorlage associated with user $userId - getting other events");

        /**
         * @var $cachingUserAccess CachingUserAccess
         */
        $cachingUserAccess = App::make(CachingUserAccess::class);
        $fines = date('Y-m-d H:i:s', strtotime('-2 days'));

        foreach ($this->events[$counterType] as $eventId => $data) {

            /**
             * @var $eventData WorkEvent
             */
            $eventData = $data['eventdata'];

            if ($data['open']) {
                if ($eventData->wiedervorlage == 1) {

                    //Wenn der letzter Oeffner nicht mehr da ist, dies dem aktuellen Benutzer zuweisen
                    $luid = $eventData->lastuserid;

                    if ($luid !== null && $this->userAbsenceFacade->isUserAbsent($luid)) {
                        $this->debug("it is a wiedervorlage and the last user $luid is not present --> giving it to $userId");
                        $this->events[$counterType][$eventId]['open'] = false;
                        return $eventId;
                    }

                    if ($luid === null) {
                        $this->debug('weird situation, no last user, giving it to current user ' . $userId);
                        $this->events[$counterType][$eventId]['open'] = false;
                        return $eventId;
                    }

                    /*
                    if ($eventData->lastuserid !== null && $eventData->lastuserid != $userId) {

                        $foreignUserId = $eventData->lastuserid;
                        $foreignUser = $cachingUserAccess->byId($eventData->lastuserid);
                        if ($foreignUser != null) {
                            if ($foreignUser->last_action_at == null || $foreignUser->last_action_at < $fines) {
                                $this->debug("returning event $eventId coz other user $foreignUserId seems not to be active");
                                $this->events[$counterType][$eventId]['open'] = false;
                                return $eventId;
                            }
                        } else {
                            $this->debug("returning event $eventId coz other user $foreignUserId seems not to exist anymore");
                            $this->events[$counterType][$eventId]['open'] = false;
                            return $eventId;
                        }
                    }
                    */

                } //keine wiedervorlage, einfach geben
                else {
                    $this->debug("returning event $eventId coz no wiedervorlage");
                    $this->events[$counterType][$eventId]['open'] = false;
                    return $eventId;
                }
            }
        }

        $this->debug('no event for user ' . $userId);
        return null;
    }

    /**
     * returns number of all events in server
     * @return int
     */
    public function countAll()
    {
        $cc = 0;
        foreach ($this->events as $a => $list) {
            $this->debug("counting $a -> " . count($list));
            $cc += count($list);
        }
        return $cc;
    }

    /**
     * returns number of all open events in server
     * @return int
     */
    public function countAllOpen()
    {
        $cc = 0;
        foreach ($this->events as $a => $list) {
            foreach ($list as $e) if ($e['open']) $cc++;
        }
        return $cc;
    }

    public function consumeByCounterTypeAndLastUserId($counterType, $userId)
    {
        return $this->findFreeEventByCounterTypeAndLastUserId($counterType, $userId);
    }

    public function reuseEventByEventId($id)
    {
        foreach ($this->events as $counterType => $eventMap) {
            if (array_key_exists($id, $this->events[$counterType])) {
                $this->events[$counterType][$id]['open'] = true;
            }
        }
    }

    public function run()
    {
        parent::run();
        $this->loadOpenEvents();
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
            $this->syncDecider->onClientIncoming();
            $this->loadAbsences();
            $this->loadOpenEvents();

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
                    $this->markForceLoadOpenEvents();
                    $response = 'ok-reload';
                    $doBreak = true;
                } else if ($buf == 'quit') {
                    break;
                } else if (strpos($buf, 'reuse-') === 0) {
                    $tmp = explode('-', $buf);
                    $eventId = $tmp[1];
                    $this->reuseEventByEventId($eventId);
                    $response = 'ok';
                    $this->syncDecider->forceSyncOnNextCheck();
                    $this->loadOpenEvents();
                    $doBreak = true;
                    $this->debug("reused event $eventId");
                } else if (strpos($buf, 'consume2-') === 0) {

                    $tmp = explode('-', $buf);
                    $counterType = $tmp[1];
                    $userId = $tmp[2];

                    $eventId = $this->consumeByCounterTypeAndLastUserId($counterType, $userId);
                    if ($eventId === null) $eventId = 0;
                    $response = "ok-$eventId";
                    $doBreak = true;

                }
                $this->debug("sending response $response");

                socket_write($msgsock, $response, strlen($response));

                if ($doBreak) break;

            } while (true);
            socket_close($msgsock);
        } while (true);
    }

    /**
     * overwrites debug and logs to output and file
     * @param $any
     */
    protected function debug($any)
    {
        QuickLog::quickWithName('event-locking-server');
        parent::debug($any);
    }

    private function dumpState($appendix)
    {
        $fp = QuickLog::createLogFileForWriting('event-locking-server-state-' . $appendix . '-' . date('YmdHis'));
        foreach ($this->events as $counterType => $eventsPerCounter) {
            foreach ($eventsPerCounter as $eid => $event) {
                $edata = $event['eventdata'];
                $line = $counterType . ';';
                $line .= $eid . ';' . $event['open'] . ';' . $edata->wiedervorlage . ';' . $edata->lastuserid;
                fwrite($fp, $line . "\r\n");
            }
        }
        fclose($fp);
    }

    private function markForceLoadOpenEvents()
    {
        $this->syncDecider->forceSyncOnNextCheck();
    }

    private function loadAbsences()
    {
        $this->userAbsenceFacade = $this->userAbsenceService->createFacadeForAllAbsences();
    }

    /**
     * reloads event set using repository
     * only if the hour has changed
     */
    private function loadOpenEvents()
    {

        if (!$this->syncDecider->needsSync()) {
            $this->debug("no sync needed");
            return;
        } else {
            $this->debug('sync needed');
        }

        $this->debug('begin loading open events...');
        $openEvents = $this->locationEventsToBeDoneService->loadOpenEvents();
        $this->debug('loaded from backend: ' . count($openEvents));

        $whitelist = [];

        //register all open events
        foreach ($openEvents as $event) {

            $eid = $event->id;

            $whitelist[] = $eid;
            $counterType = CounterType::DEFAULT;

            if ($event->wiedervorlage == 1) {
                $this->debug('wiedervorlage ' . $eid . ' fuer ' . $event->lastuserid);
            }

            $this->registerEvent($counterType, $eid, $event);
        }

        $this->debug("gleiche Events ab (whitelist)");
        foreach ($this->events as $counterType => $eventsPerCounter) {
            $this->debug("Counter $counterType");
            $newList = [];
            foreach ($eventsPerCounter as $eid => $edata) {
                //bereits erledigt --> behalten
                if (!$edata['open']) {
                    $newList[$eid] = $edata;
                } else {
                    if (in_array($eid, $whitelist)) {
                        $newList[$eid] = $edata;
                    }
                }
            }
            $this->debug('Anzahl alte Liste: ' . count($eventsPerCounter));
            $this->debug('Anzahl neue Liste: ' . count($newList));
            $this->events[$counterType] = $newList;
        }

        $this->debug('serving open events: ' . $this->countAllOpen());
        //$this->dumpState('afterLoadOpenEvents');
        $this->debug('state dumped');
    }
}
