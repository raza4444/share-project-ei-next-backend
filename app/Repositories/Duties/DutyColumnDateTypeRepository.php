<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyColumnDateType;
use App\Repositories\AbstractRepository;

class DutyColumnDateTypeRepository extends AbstractRepository
{

  public function __construct()
  {
    parent::__construct(DutyColumnDateType::class);
  }

  public function all()
  {
    return $this->query()->get();
  }

}
