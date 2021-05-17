<?php

/**
 * by Samuel Leicht
 */

namespace App\Services\Duties;

use Illuminate\Support\Facades\DB;

class DutyTaskTemplService
{
  /**
   * returns all duty task templates for a specific duty row
   * (configurator)
   * 
   * @param int $blockId the id of the duty block
   * @param int $rowId the id of the duty row templ
   * 
   * @return array
   */
  public function dutyTaskTemplatesForDutyRow($blockId, $rowId)
  {
    $q = "
        SELECT drt.pos, dt.createOnce, dt.id, dt.name, dt.description FROM duty_task_templates AS dt
        INNER JOIN duty_rows_templ_tasks_templ AS drt
        ON drt.dutyBlockId =" . $blockId . "
        AND drt.dutyTaskId = dt.id 
        AND drt.dutyRowId=" . $rowId . "
        ORDER BY drt.pos";

    return DB::select($q);
  }

  /**
   * links a duty task to a specific duty row
   * (configurator)
   * 
   * @param int $blockId the id of the duty block
   * @param int $rowId the id of the duty row templ
   * @param int $taskId the id of the duty task templ
   * @param int $pos the position in the list of tasks of the task templ
   * 
   * @return array
   */
  public function linkTaskToRow($blockId, $rowId, $taskId, $pos)
  {
    DB::table('duty_rows_templ_tasks_templ')->insert([
      'dutyBlockId' => $blockId,
      'dutyRowId' => $rowId,
      'dutyTaskId' => $taskId,
      'pos' => $pos
    ]);
  }

  /**
   * update task order of a specific duty row
   * (configurator)
   * 
   * @param array $orderedTasks the list of ordered task templates
   * @param int $blockId the id of the duty block
   * @param int $rowId the id of the duty row templ
   * 
   * @return array
   */
  public function updateTaskOrderForRow($orderedTasks, $blockId, $rowId)
  {
    foreach ($orderedTasks as $task) {
      DB::table('duty_rows_templ_tasks_templ')
        ->where('dutyBlockId', $blockId)
        ->where('dutyRowId', $rowId)
        ->where('dutyTaskId', $task['id'])
        ->update([
          'pos' => $task['pos']
        ]);
    }
  }

  /**
   * unlinks a duty task from a specific duty row
   * (configurator)
   * 
   * @param int $blockId the id of the duty block
   * @param int $rowId the id of the duty row templ
   * @param int $taskId the id of the duty task templ
   * 
   * @return array
   */
  public function unlinkTaskFromRow($blockId, $rowId, $taskId)
  {
    DB::table('duty_rows_templ_tasks_templ')
      ->where('dutyBlockId', $blockId)
      ->where('dutyRowId', $rowId)
      ->where('dutyTaskId', $taskId)
      ->delete();

    DB::table('duty_follow_ups')
      ->where('dutyBlockId', $blockId)
      ->where('dutyRowTemplId', $rowId)
      ->where('dutyTaskTemplId', $taskId)
      ->delete();
  }

}
