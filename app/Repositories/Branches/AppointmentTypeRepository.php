<?php

/**
 * by stephan scheide
 */

namespace App\Repositories\Branches;

use App\Entities\Branches\AppointmentType;
use App\Repositories\AbstractRepository;

class AppointmentTypeRepository extends AbstractRepository
{
  public function __construct()
  {
    parent::__construct(AppointmentType::class);
  }

  public function all() 
  {
    return $this->query()->get();
  }
}
