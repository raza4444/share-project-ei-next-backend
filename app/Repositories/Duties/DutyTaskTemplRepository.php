<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyTaskTempl;
use App\Repositories\AbstractRepository;

class DutyTaskTemplRepository extends AbstractRepository
{
  public function __construct()
  {
    parent::__construct(DutyTaskTempl::class);
  }

  public function getAll()
  {
    return $this->query()->get();
  }
}
