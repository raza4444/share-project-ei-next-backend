<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Tasks;


use App\Entities\Tasks\CounterTaskEvent;
use App\Http\Controllers\AbstractInternController;
use App\Repositories\Tasks\CounterTaskEventRepository;
use App\Servers\Tasks\TaskFilter;
use App\Servers\Tasks\TaskReservationServerClient;
use App\Services\Tasks\BusinessCounterTaskEventService;
use App\Utils\DateTimeUtils;

class CounterTaskController extends AbstractInternController
{

    private $counterTaskEventRepository;

    private $businessCounterTaskEventService;

    public function __construct(
        CounterTaskEventRepository $counterTaskEventRepository,
        BusinessCounterTaskEventService $businessCounterTaskEventService
    )
    {
        $this->counterTaskEventRepository = $counterTaskEventRepository;
        $this->businessCounterTaskEventService = $businessCounterTaskEventService;
    }

    public function countOpenEvents($id)
    {
        $client = new TaskReservationServerClient();
        $count = $client->countOpen(TaskFilter::newDefault($id, $this->getCurrentUserId()));
        // $count = $this->counterTaskEventRepository->countOpenOfCounterTask($id);
        $result = ['count' => $count];
        return $this->singleJson($result);
    }

    public function nextEventId($id)
    {
        $client = new TaskReservationServerClient();
        $id = $client->nextIdFor(TaskFilter::newDefault($id, $this->getCurrentUserId()));
        if ($id < 1 || !$id) {
            $id = 0;
        }
        return $this->singleJson(['id' => $id]);
    }

    public function eventByIdForWork($id)
    {
        $event = $this->counterTaskEventRepository->byIdForWork($id);
        if ($event == null) {
            return $this->notFound();
        }
        return $this->singleJson($event);
    }

    public function markAsDone($id)
    {
        /**
         * @var CounterTaskEvent $event
         */

        //pruefen, ob noch aktuell
        $event = $this->counterTaskEventRepository->byId($id);
        if ($event == null) {
            return $this->notFound();
        }

        $userId = $this->getCurrentUserId();

        //Countereignis auf erledigt setzen und speichern
        $event->done = 1;
        $event->doneBy = $userId;
        $event->finishedAt = DateTimeUtils::nowAsString();
        $event->save();

        $event->markMainTaskAsDone($userId);

        //Das Ereignis "Countertask ereignis" an Service uebergeben, so dass ggf. Folgeereignisse erzeugt werden
        $this->businessCounterTaskEventService->handleCounterTaskEventDone($event);

        return $this->noContent();
    }

    public function eventsForOverview()
    {
        $list = $this->counterTaskEventRepository->findForOverview();
        return $this->singleJson($list);
    }

}
