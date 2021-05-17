<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use App\Entities\Branches\Location;
use App\Entities\Branches\LocationEvent;

class LocationDeletionService
{

    public function deleteLocationAndEventsLogically($locationId)
    {
        Location::query()
            ->where('id', '=', $locationId)
            ->delete();

        LocationEvent::query()
            ->where('schoolid', '=', $locationId)
            ->delete();
    }

}
