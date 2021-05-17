<?php

/**
 * by Samuel Leicht
 */

namespace App\Services\Duties;

use Illuminate\Support\Facades\DB;

class DutyFollowUpService
{
  /**
   * returns all follow ups for a specific duty task id
   * which doesn't yet exist if create once is set
   *
   * @param $taskId the id of the duty task
   *  
   * @return array
   */
  public function getFollowUpsForTask($taskId)
  {
    $q = '
    SELECT 
    dfu.id,
    dfu.followUpDutyTaskTemplId, 
    dfu.followUpDutyRowTemplId, 
    dfu.targetDutyBlockId, 
    dr.locationId,
    dbrt.dutyRowTemplId AS targetDutyRowTemplId, 
    dfu.followUpInteractionTypeId,
    dfu.interactionMsg,
    drtmpl.createOnce AS createRowOnce,
    dtt.createOnce AS createTaskOnce
    FROM duty_follow_ups AS dfu
    LEFT JOIN duty_row_templates as drtmpl
    ON drtmpl.id = dfu.followUpDutyRowTemplId
    LEFT JOIN duty_task_templates as dtt
    ON dtt.id = dfu.followUpDutyTaskTemplId
    LEFT JOIN duty_blocks_rows_templ as dbrt
    ON dbrt.id = dfu.targetDutyBlockRowTemplId
    OR (dbrt.dutyBlockId = dfu.targetDutyBlockId AND dbrt.dutyRowTemplId = dfu.followUpDutyRowTemplId)
    INNER JOIN duty_rows AS dr
    ON dr.dutyRowTemplId = dfu.dutyRowTemplId
    AND dr.dutyBlockId = dfu.dutyBlockId
    INNER JOIN duty_rows_tasks AS drt
    ON drt.dutyTaskTemplId = dfu.dutyTaskTemplId
    AND drt.dutyRowId = dr.id
    AND drt.id = ' . $taskId;

    // return DB::select($q);

    $followUps = DB::select($q);
    $filteredFollowUps = [];

    for ($i = 0; $i < sizeof($followUps); $i++) {

      if ($followUps[$i]->followUpDutyRowTemplId !== null) { // Follow Up is a row

        if ($followUps[$i]->createRowOnce === 1) { // Follow Up row should only get created once

          // Check if row already exists for location
          $existingRows = DB::table('duty_rows')
            ->where('locationId', $followUps[$i]->locationId)
            ->where('dutyRowTemplId', $followUps[$i]->followUpDutyRowTemplId)
            ->get();

          if (count($existingRows) === 0) {
            array_push($filteredFollowUps, $followUps[$i]);
          }
        } else {
          array_push($filteredFollowUps, $followUps[$i]);
        }
      } else if ($followUps[$i]->followUpDutyTaskTemplId !== null) { // Follow Up is a task

        if ($followUps[$i]->createTaskOnce === 1) { // Follow Up task should only get created once

          // Check if task already exists for location and is marked as done
          $existingRows = DB::table('duty_rows_tasks')
            ->join('duty_rows', function($query) use ($followUps, $i) {
              $query->on('duty_rows_tasks.dutyRowId', 'duty_rows.id');
              $query->on('duty_rows.locationId', DB::raw($followUps[$i]->locationId));
              $query->on('duty_rows_tasks.dutyTaskTemplId', DB::raw($followUps[$i]->followUpDutyTaskTemplId));
              $query->on('duty_rows_tasks.done', DB::raw('1'));
            })
            ->get();

          if (count($existingRows) === 0) {
            array_push($filteredFollowUps, $followUps[$i]);
          }
        } else {
          array_push($filteredFollowUps, $followUps[$i]);
        }
      }
    }

    return $filteredFollowUps;
  }
}
