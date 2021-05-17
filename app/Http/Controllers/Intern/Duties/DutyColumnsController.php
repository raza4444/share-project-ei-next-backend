<?php

/**
 * by Samuel Leicht
 */

namespace App\Http\Controllers\Intern\Duties;

use App\Http\Controllers\AbstractInternController;
use App\Repositories\Duties\DutyColumnDateTypeRepository;
use App\Repositories\Duties\DutyColumnRepository;
use App\Repositories\Duties\DutyRowRepository;

class DutyColumnsController extends AbstractInternController
{
  private $dutyColumnDateTypeRepository;
  private $dutyColumnRepository;

  public function __construct(
    DutyRowRepository $dutyRowRepository,
    DutyColumnDateTypeRepository $dutyColumnDateTypeRepository,
    DutyColumnRepository $dutyColumnRepository
  ) {
    $this->dutyRowRepository = $dutyRowRepository;
    $this->dutyColumnDateTypeRepository = $dutyColumnDateTypeRepository;
    $this->dutyColumnRepository = $dutyColumnRepository;
  }

  public function allColumns() {
    return $this->singleJson($this->dutyColumnRepository->all());
  }

  public function allDateTypes() {
    return $this->singleJson($this->dutyColumnDateTypeRepository->all());
  }
}
