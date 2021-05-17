<?php
/**
 * by stephan scheide
 */

namespace App\Services\Core;


use App\Entities\Core\UserAbsence;

class UserAbsenceFacade
{

    private $uids = [];

    public function addAbsenceForNow(UserAbsence $absence)
    {
        $this->uids[] = $absence->userId;
    }

    public function isUserAbsent($userId)
    {
        return in_array($userId, $this->uids);
    }

}
