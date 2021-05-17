<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyTrigger;
use App\Repositories\AbstractRepository;

class DutyTriggerRepository extends AbstractRepository
{

  public function __construct()
  {
    parent::__construct(DutyTrigger::class);
  }

  public function all()
  {
    return $this->query()->get();
  }
}
