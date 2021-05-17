<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyRowTemplTrigger;
use App\Repositories\AbstractRepository;

class DutyRowTemplTriggerRepository extends AbstractRepository
{

  public function __construct()
  {
    parent::__construct(DutyRowTemplTrigger::class);
  }

  public function findByRowTemplId($id)
  {
    return $this->query()->where('dutyRowTemplId', '=', $id)->first();
  }
  
}
