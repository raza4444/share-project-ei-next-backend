<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Branches;


use App\Entities\Branches\LocationEvent;
use App\Repositories\AbstractRepository;

class LocationEventRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct(LocationEvent::class);
    }

    /**
     * @param $id
     * @return LocationEvent|null
     */
    public function findForWorkById($id)
    {
        /**
         * @var $event LocationEvent
         */
        return LocationEvent::query()
            ->whereNull('deleted_at')
            ->whereNull('lockedUserId')
            ->with('location')
            ->with('notes')
            ->with('notes.user')
            ->where('id', '=', $id)
            ->first();

    }

    public function setResultToNotInterestedByLocationId($locationId)
    {
        LocationEvent::query()
            ->whereNull('deleted_at')
            ->where('done', '=', '0')
            ->where('schoolid', '=', $locationId)
            ->update(['done' => 1, 'result' => 'noInterest']);
    }

    public function byBasicFilter(BasicEventFilter $filter)
    {
        $q = LocationEvent::query();
        $todayStart = date('Y-m-d') . ' 00:00:00';

        if ($filter->view == BasicEventFilter::VIEW_WIEDERVORLAGE) {
            $q
                ->where('wiedervorlage', '=', 1)
                ->where('showAfter', '>=', $todayStart)
                ->orderBy('showAfter', 'asc')
                ->with('tracks')
                ->with('tracks.user')
                ->with('location');
        }

        return $q->get();
    }

}
