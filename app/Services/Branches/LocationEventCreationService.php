<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use App\Entities\Branches\Location;
use App\Entities\Branches\LocationEvent;
use Illuminate\Support\Carbon;

class LocationEventCreationService
{

    /**
     * erzeugt ein Ereignis fuer ein Unternehmen
     * @param Location $location
     * @return LocationEvent
     */
    public function createForLocation(Location $location)
    {
        $now = Carbon::now();
        $event = new LocationEvent();
        $event->timestamp = $now;
        $event->showAfter = $now;
        $event->schoolid = $location->id;
        $event->save();
        return $event;
    }

}
