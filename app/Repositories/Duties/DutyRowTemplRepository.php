<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyRowTempl;
use App\Repositories\AbstractRepository;
use App\Entities\Core\PermissionType;
use Illuminate\Support\Facades\DB;

class DutyRowTemplRepository extends AbstractRepository
{

  public function __construct()
  {
    parent::__construct(DutyRowTempl::class);
  }

  public function getAll()
  {
    return $this->query()->get();
  }

  public function taskTemplExists($blockId, $rowTemplId, $taskTemplId)
  {
    $exists = DB::table('duty_rows_templ_tasks_templ')
      ->where('dutyBlockId', $blockId)
      ->where('dutyRowId', $rowTemplId)
      ->where('dutyTaskId', $taskTemplId);

    if (is_null($exists)) {
      return false;
    }

    return true;
  }


  /**
   * @return array
   */
  public function getDutyConfiguratorTaskRowsPermissions()
  {
    return [
      PermissionType::DUTY_CONFIGURATOR_TASK_LINES_SHOW,
      PermissionType::DUTY_CONFIGURATOR_TASK_LINES_ADD,
      PermissionType::DUTY_CONFIGURATOR_TASK_LINES_EDIT,
      PermissionType::DUTY_CONFIGURATOR_TASK_LINES_DELETE
    ];
  }
}
