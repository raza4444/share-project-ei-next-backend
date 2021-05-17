<?php

/**
 * by Samuel Leicht
 */

namespace App\Http\Controllers\Intern\Duties;

use App\Http\Controllers\AbstractInternController;
use App\Repositories\Duties\DutyRowRepository;
use App\Services\Duties\DutyRowService;
use DateTime;

class DutyRowController extends AbstractInternController
{
  private $dutyRowService;
  private $dutyRowRepository;

  public function __construct(
    DutyRowService $dutyRowService,
    DutyRowRepository $dutyRowRepository
  ) {
    $this->dutyRowService = $dutyRowService;
    $this->dutyRowRepository = $dutyRowRepository;
  }

  public function getRowWithAddInfo($dutyRowId)
  {
    return $this->singleJson($this->dutyRowService->getRowWithAddInfo($dutyRowId));
  }

  public function createRowsAndTasksForNewAppointment($appointmentId, $locId, $callerRowId)
  {
    $this->dutyRowService->createRowsAndTasksForNewAppointment($appointmentId, $locId, $callerRowId);
  }

  public function updateAppointmentInRows($appointmentId, $newAppointmentId)
  {
    $this->dutyRowRepository->updateAppointmentInRows($appointmentId, $newAppointmentId);
  }

  public function getAllInclDataForBlock($blockId)
  {
    // Release rows which haven't been updated for two hours
    $this->dutyRowRepository->releaseAbandonedRows();

    $rows = $this->dutyRowService->allDataForBlock($blockId);
    return $this->singleJson($rows);
  }

  public function getAllInclDataForCompany($locId)
  {

    $rows = $this->dutyRowRepository->allRowsInclDataForCompany($locId);
    return $this->singleJson($rows);
  }

  /**
   * @param string $timeInterval
   * @return void
   */

  public function getUserBreaksData(string $timeInterval)
  {
    $availableTimeInterval = ['today', 'current-week', 'current-month'];
    if (in_array($timeInterval, $availableTimeInterval)) {
      $rows = $this->dutyRowService->getDutyRowsForUserBreaks($timeInterval);
      return $this->singleJson($rows);
    }
  }

  public function assign($rowId)
  {
    $row = $this->dutyRowRepository->byId($rowId);
    if ($row == null) {
      return $this->notFound();
    }

    $row["updated_by"] = $this->getCurrentUserId();

    if ($row["assignedUserId"] == null) {

      $row["assignedUserId"] = $this->getCurrentUserId();
      $row["started_at"] = new DateTime('now');
    } else if ($row["assignedUserId"] !== $this->getCurrentUserId()) {
      return $this->conflict();
    }

    $row->save();

    return $this->noContent();
  }

  public function release($rowId)
  {
    $row = $this->dutyRowRepository->byId($rowId);
    if ($row == null) {
      return $this->notFound();
    }

    $row["assignedUserId"] = null;
    $row["updated_by"] = $this->getCurrentUserId();
    $row->save();

    return $this->noContent();
  }

  public function close($rowId)
  {
    $this->dutyRowRepository->close($rowId);
  }
}
