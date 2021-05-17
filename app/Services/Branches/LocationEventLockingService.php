<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use App\Entities\Branches\CounterType;
use App\Servers\EventServerClient;

class LocationEventLockingService
{

    public function isLockingSystemAvailable()
    {
        $client = new EventServerClient();
        return $client->isServerAvailable();
    }

    public function findNextFreeEventIdAndLock($counterType = CounterType::DEFAULT)
    {
        $client = new EventServerClient();
        $id = $client->nextFreeEventId($counterType);
        if ($id === 0) return null;
        return $id;
    }

    public function findNextFreeEventIdAndLockForUser($userId, $counterType = CounterType::DEFAULT)
    {
        $client = new EventServerClient();
        $id = $client->nextFreeEventIdWithUserAssociation($counterType, $userId);
        if ($id === 0) return null;
        return $id;
    }

}
