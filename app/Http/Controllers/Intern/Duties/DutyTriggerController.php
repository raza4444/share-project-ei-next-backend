<?php

/**
 * by Samuel Leicht
 */

namespace App\Http\Controllers\Intern\Duties;

use App\Http\Controllers\AbstractInternController;
use App\Repositories\Duties\DutyTriggerRepository;

class DutyTriggerController extends AbstractInternController
{
  private $dutyTriggerRepository;

  public function __construct(
    DutyTriggerRepository $dutyTriggerRepository
  ) {
    $this->dutyTriggerRepository = $dutyTriggerRepository;
  }

  public function all() {
    return $this->dutyTriggerRepository->all();
  }
}
