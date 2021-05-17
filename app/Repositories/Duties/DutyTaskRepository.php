<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyTask;
use App\Repositories\AbstractRepository;
use App\Entities\Core\PermissionType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DutyTaskRepository extends AbstractRepository
{
  public function __construct()
  {
    parent::__construct(DutyTask::class);
  }

  /**
   * Returns all finished tasks for a given location id
   * 
   * @param number $locationId the id of the location 
   * 
   * @return array
   */
  public function findAllDoneForLocation($locationId)
  {
    return DB::table('duty_rows AS dr')
      ->join('duty_rows_tasks AS drt', 'drt.dutyRowId', 'dr.id')
      ->join('duty_row_templates AS drte', 'drte.id', 'dr.dutyRowTemplId')
      ->join('duty_task_templates AS dtt', 'dtt.id', 'drt.dutyTaskTemplId')
      ->where('drt.done', 1)
      ->where('dr.locationId', $locationId)
      ->select([
        'drte.name AS rowTemplateName',
        'dtt.name AS taskTemplateName',
        'drt.updated_at'
      ])
      ->get();
  }

  public function getAll()
  {
    return $this->query()->get();
  }

  /**
   * updates the status of a given task
   * 
   * @param $taskId the id of the task
   * @param $status the new status of the task
   */
  public function updateStatus($taskId, $status, $userId)
  {
    return $this->query()
      ->where('id', $taskId)
      ->update([
        'done' => $status,
        'updatedBy' => $userId
      ]);
  }

  /**
   * returns all finished tasks including associated row and block
   *
   * @return array
   */
  public function finishedTodayTasks()
  {
    return $this->query()
      ->join('duty_rows AS dr', 'dr.id', 'duty_rows_tasks.dutyRowId')
      ->join('duty_block AS db', 'db.id', 'dr.dutyBlockId')
      ->join('duty_row_templates AS drtmpl', 'drtmpl.id', 'dr.dutyRowTemplId')
      ->join('duty_task_templates AS dttmpl', 'dttmpl.id', 'duty_rows_tasks.dutyTaskTemplId')
      ->join('users AS u', 'u.id', 'duty_rows_tasks.updatedBy')
      ->join('campaign_locations AS cl', 'cl.id', 'dr.locationId')
      ->where('duty_rows_tasks.done', 1)
      ->whereDate('duty_rows_tasks.updated_at', Carbon::today())
      ->select([
        'cl.title as locationName',
        'cl.id as locationId',
        'drtmpl.name as dutyRowTemplName',
        'dttmpl.name as dutyTaskTemplName',
        'u.username as userName',
        'dr.started_at as startedAt',
        'duty_rows_tasks.updated_at as finishedAt'

      ])
      ->orderBy('duty_rows_tasks.updated_at', 'desc')
      ->get();
  }

  public function finishedYesterdayTasks()
  {
    return $this->query()
      ->join('duty_rows AS dr', 'dr.id', 'duty_rows_tasks.dutyRowId')
      ->join('duty_block AS db', 'db.id', 'dr.dutyBlockId')
      ->join('duty_row_templates AS drtmpl', 'drtmpl.id', 'dr.dutyRowTemplId')
      ->join('duty_task_templates AS dttmpl', 'dttmpl.id', 'duty_rows_tasks.dutyTaskTemplId')
      ->join('users AS u', 'u.id', 'duty_rows_tasks.updatedBy')
      ->join('campaign_locations AS cl', 'cl.id', 'dr.locationId')
      ->where('duty_rows_tasks.done', 1)
      ->whereDate('duty_rows_tasks.updated_at', Carbon::yesterday())
      ->select([
        'cl.title as locationName',
        'cl.id as locationId',
        'drtmpl.name as dutyRowTemplName',
        'dttmpl.name as dutyTaskTemplName',
        'u.username as userName',
        'dr.started_at as startedAt',
        'duty_rows_tasks.updated_at as finishedAt'

      ])
      ->orderBy('duty_rows_tasks.updated_at', 'desc')
      ->get();
  }
  public function finishedLastMonthTasks()
  {
    return $this->query()
      ->join('duty_rows AS dr', 'dr.id', 'duty_rows_tasks.dutyRowId')
      ->join('duty_block AS db', 'db.id', 'dr.dutyBlockId')
      ->join('duty_row_templates AS drtmpl', 'drtmpl.id', 'dr.dutyRowTemplId')
      ->join('duty_task_templates AS dttmpl', 'dttmpl.id', 'duty_rows_tasks.dutyTaskTemplId')
      ->join('users AS u', 'u.id', 'duty_rows_tasks.updatedBy')
      ->join('campaign_locations AS cl', 'cl.id', 'dr.locationId')
      ->where('duty_rows_tasks.done', 1)
      ->where('duty_rows_tasks.updated_at', '>=',  Carbon::now()->subDays(30))
      ->select([
        'cl.title as locationName',
        'cl.id as locationId',
        'drtmpl.name as dutyRowTemplName',
        'dttmpl.name as dutyTaskTemplName',
        'u.username as userName',
        'dr.started_at as startedAt',
        'duty_rows_tasks.updated_at as finishedAt'

      ])
      ->orderBy('duty_rows_tasks.updated_at', 'desc')
      ->get();
  }

  /**
   * @return array
   */
  public function getDutyConfiguratorTasksPermissions()
  {
    return [
      PermissionType::DUTY_CONFIGURATOR_TASKS_SHOW,
      PermissionType::DUTY_CONFIGURATOR_TASKS_ADD,
      PermissionType::DUTY_CONFIGURATOR_TASKS_EDIT,
      PermissionType::DUTY_CONFIGURATOR_TASKS_DELETE
    ];
  }

}
