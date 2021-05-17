<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Tasks;


use App\Entities\Tasks\CounterTaskEvent;
use App\Repositories\AbstractRepository;

class CounterTaskEventRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct(CounterTaskEvent::class);
    }

    public function countOpenOfCounterTask($counterTaskId)
    {
        return $this->query()
            ->where('counterTaskId', '=', $counterTaskId)
            ->where('done', '=', 0)
            ->count();
    }

    /**
     * @param $counterTaskId
     * @return CounterTaskEvent|null
     */
    public function findNextOpenEvent($counterTaskId)
    {
        return $this->query()
            ->where('counterTaskId', '=', $counterTaskId)
            ->where('done', '=', 0)
            ->first();
    }

    public function byIdForWork($id)
    {
        return $this->query()
            ->where('id', '=', $id)
            ->with('mainTask')
            ->with('locationEventAppointment')
            ->with('locationEventAppointment.event')
            ->with('locationEventAppointment.event.location')
            ->first();
    }

    /**
     * returns all open events
     *
     * @return CounterTaskEvent[]
     */
    public function findAllOpen()
    {
        return $this->query()
            ->where('done', '=', 0)
            ->orderBy('dueAt', 'asc')
            ->get();
    }

    public function findForOverview()
    {
        return $this->query()
            ->where('done', '=', 0)
            ->with('mainTask')
            ->with('counterTask')
            ->with('locationEventAppointment')
            ->with('locationEventAppointment.event')
            ->with('locationEventAppointment.event.location')
            ->orderBy('counterTaskId')
            ->orderBy('dueAt')
            ->get();
    }

}
