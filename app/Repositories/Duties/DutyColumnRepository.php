<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyColumn;
use App\Repositories\AbstractRepository;

class DutyColumnRepository extends AbstractRepository
{

  public function __construct()
  {
    parent::__construct(DutyColumn::class);
  }

  public function all()
  {
    return $this->query()->get();
  }

}
