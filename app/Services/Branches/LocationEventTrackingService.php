<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use App\Entities\Branches\LocationEventTrack;
use App\Utils\DateTimeUtils;

class LocationEventTrackingService
{

    public function trackEventOpen($eventId, $userId)
    {
        $this->track($eventId, $userId, 'opened', null, null, null, null, null);
    }

    public function trackResultSaved($eventId, $userId, $result, $notice, $appointmentAt, $appointmentTypeId)
    {
        $this->track($eventId, $userId, 'result', $result, $notice, $appointmentAt, $appointmentTypeId, null);
    }

    public function trackResultSavedShowAgain($eventId, $userId, $showAgainAt)
    {
        $this->track($eventId, $userId, 'result', 'showAgain', null, null, null, $showAgainAt);
    }

    public function track($eventId, $userId, $action, $result, $notice, $appointmentAt, $appointmentTypeId, $showAgainAt)
    {
        $track = new LocationEventTrack();
        $track->eventId = $eventId;
        $track->userId = $userId;
        $track->action = $action;
        $track->result = $result;
        $track->notice = $notice;
        $track->appointmentAt = $appointmentAt;
        $track->appointmentTypeId = $appointmentTypeId;
        $track->showAgainAt = $showAgainAt;
        $track->trackedAt = DateTimeUtils::nowAsString();
        $track->save();
    }

}
