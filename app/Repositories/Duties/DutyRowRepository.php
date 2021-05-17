<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyRow;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Carbon;
use App\Entities\Core\PermissionType;

class DutyRowRepository extends AbstractRepository
{

  public function __construct()
  {
    parent::__construct(DutyRow::class);
  }

  public function close($rowId)
  {
    $this->query()
      ->where('id', $rowId)
      ->update([
        'done' => '1'
      ]);
  }

  public function updateAppointmentInRows($appointmentId, $newAppointmentId)
  {
    $this->query()
      ->where('appointmentId', $appointmentId)
      ->update([
        'appointmentId' => $newAppointmentId
      ]);
  }

  public function releaseAbandonedRows()
  {
    $this->query()
      ->where('assignedUserId', '<>', null)
      ->where('updated_at', '<', Carbon::parse('-2 hours'))
      ->update([
        'assignedUserId' => null
      ]);
  }

  public function findAppointmentForRow($rowId)
  {
    $appointment = $this->query()
      ->select('appointmentId')
      ->where('id', $rowId)
      ->get()
      ->first();

    return $appointment['appointmentId'];
  }

  public function allRowsInclDataForCompany($locId) {
    return $this->query()
      ->join('duty_row_templates', 'duty_row_templates.id', 'duty_rows.dutyRowTemplId')
      ->join('duty_block', 'duty_block.id', 'duty_rows.dutyBlockId')
      // ->join('appointments', 'appointments.id', 'duty_rows.appointmentId')
      ->where('duty_rows.locationId', $locId)
      ->where('duty_rows.done', '0')
      ->get([
        'duty_block.name as blockName',
        'duty_row_templates.name as rowName',
        // 'appointments.when',
        'duty_rows.updated_at'
      ]);
  }

  public function updateUpdatedBy($rowId, $userId)
  {
    $this->query()
      ->where('id', $rowId)
      ->update([
        'updated_by' => $userId
      ]);
  }


  public function dutyRowsInclDataForTodayBreaks() {
    return $this->query()
    ->join('users', 'users.id', 'duty_rows.updated_by')
    ->where('users.admin', 0)
    ->whereDate('duty_rows.updated_at', Carbon::today())
    ->whereDate('duty_rows.started_at', Carbon::today())
    ->orderBy('duty_rows.updated_at', 'asc')
    ->get([
        'users.id as userId',
        'username',
        'duty_rows.started_at as startedAt',
        'duty_rows.updated_at as finishedAt'
      ]);
   }

   public function dutyRowsInclDataForCurrentWeekBreaks() {
    return $this->query()
    ->join('users', 'users.id', 'duty_rows.updated_by')
    ->where('users.admin', 0)
    ->where('duty_rows.started_at', '>=', Carbon::now()->startOfWeek())
    ->where('duty_rows.updated_at', '>=', Carbon::now()->startOfWeek())
    ->where('duty_rows.started_at', '<=', Carbon::now())
    ->where('duty_rows.updated_at', '<=', Carbon::now())
    ->orderBy('duty_rows.updated_at', 'asc')
    ->get([
        'users.id as userId',
        'username',
        'duty_rows.started_at as startedAt',
        'duty_rows.updated_at as finishedAt'
      ]);
   }
  
   public function dutyRowsInclDataForCurrentMonthBreaks() {
    return $this->query()
    ->join('users', 'users.id', 'duty_rows.updated_by')
    ->where('users.admin', 0)
    ->where('duty_rows.started_at', '>=', Carbon::now()->startOfMonth())
    ->where('duty_rows.updated_at', '>=', Carbon::now()->startOfMonth())
    ->where('duty_rows.started_at', '<=', Carbon::now())
    ->where('duty_rows.updated_at', '<=', Carbon::now())
    ->orderBy('duty_rows.updated_at', 'asc')
    ->get([
        'users.id as userId',
        'username',
        'duty_rows.started_at as startedAt',
        'duty_rows.updated_at as finishedAt'
      ]);
   }
   
   public function getPermissionOfTasksBlock() {
    return [
      PermissionType::COMPANY_DETAILS_OPEN_TASKS_BLOCK_SHOW,
      PermissionType::COMPANY_DETAILS_DONE_TASKS_BLOCK_SHOW,
    ];
   }
}
