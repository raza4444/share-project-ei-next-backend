<?php

/**
 * by Samuel Leicht
 */

namespace App\Http\Controllers\Intern\Duties;

use App\Http\Controllers\AbstractInternController;
use App\Repositories\Duties\DutyFollowUpInteractionTypeRepository;

class DutyFollowUpInteractionTypeController extends AbstractInternController
{
  private $dutyFollowUpInteractionTypeRepository;

  public function __construct(
    DutyFollowUpInteractionTypeRepository $dutyFollowUpInteractionTypeRepository
  ) {
    $this->dutyFollowUpInteractionTypeRepository = $dutyFollowUpInteractionTypeRepository;
  }

  public function getAll() {
    return $this->singleJson($this->dutyFollowUpInteractionTypeRepository->getAll());
  }
}
