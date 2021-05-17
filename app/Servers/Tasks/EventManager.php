<?php
/**
 * by stephan scheide
 */

namespace App\Servers\Tasks;


use App\Entities\Tasks\CounterTaskEvent;

class EventManager
{

    public $eventsPerCounter = [];

    public $eventRuntimeInfoById = [];

    public function __construct()
    {
        $this->clear();
    }

    public function addEvent(CounterTaskEvent $event)
    {
        $cid = $event->counterTaskId;
        if (!array_key_exists($cid, $this->eventsPerCounter)) {
            $this->eventsPerCounter[$cid] = [];
        }

        $this->eventsPerCounter[$cid][$event->id] = $event;
        $this->eventRuntimeInfoById[$event->id] = new EventRuntimeInfo();

    }

    public function clear()
    {
        $this->eventsPerCounter = [];
        $this->eventRuntimeInfoById = [];
    }

    /**
     * @param $id
     * @return CounterTaskEvent|null
     */
    public function eventById($id)
    {
        foreach ($this->eventsPerCounter as $counterTaskId => $eventMap) {
            foreach ($eventMap as $eventId => $event) {
                if ($eventId == $id) {
                    return $event;
                }
            }
        }
        return null;
    }

    /**
     * @param $id
     * @return EventRuntimeInfo|null
     */
    public function runtimeInfoByEventId($id)
    {
        return array_key_exists($id, $this->eventRuntimeInfoById) ? $this->eventRuntimeInfoById[$id] : null;
    }

    /**
     * @param $cid
     * @return int
     */
    public function countOpenByCounterTaskId($cid)
    {
        $cc = 0;
        foreach ($this->eventsPerCounter as $counterTaskId => $eventMap) {
            if ($counterTaskId == $cid) {
                foreach ($eventMap as $eid => $event) {
                    $info = $this->runtimeInfoByEventId($eid);
                    if ($info && $info->open) {
                        $cc++;
                    }
                }
            }
        }
        return $cc;
    }

    public function nextOpenEventIdFor($cid, $userId)
    {
        foreach ($this->eventsPerCounter as $counterTaskId => $eventMap) {
            if ($counterTaskId == $cid) {
                foreach ($eventMap as $eid => $event) {
                    $info = $this->runtimeInfoByEventId($eid);
                    if ($info && $info->open) {
                        $id = $event->id;
                        $info->open = false;
                        return $id;
                    }
                }
            }
        }

        return null;
    }

}