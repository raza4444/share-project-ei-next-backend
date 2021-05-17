<?php

/**
 * by Samuel Leicht
 */

namespace App\Services\Duties;

use Illuminate\Support\Facades\DB;

class DutyRowTemplService
{
  /**
   * returns all duty rows for a specific duty block
   *
   * @param $blockId the id of the duty block
   * 
   * @return array
   */
  public function getAllForBlock($blockId)
  {
    $q = "
    SELECT dr.id, dr.name, dr.description, dr.createOnce, dr.minFinishedTasksToCompl, dt.id as dutyTriggerId, dbrt.pos
    FROM duty_row_templates as dr 
    INNER JOIN duty_blocks_rows_templ as dbrt ON dbrt.dutyBlockId = " . $blockId . " 
    AND dbrt.dutyRowTemplId = dr.id
    INNER JOIN duty_rows_templ_trigger as drtt 
    ON drtt.dutyRowTemplId = dr.id
    INNER JOIN duty_triggers as dt ON dt.id = drtt.dutyTriggerId 
    ORDER BY dbrt.pos";

    return DB::select($q);
  }

  /**
   * returns all duty columns for a specific duty row template
   *
   * @param $rowId the id of the duty row template
   * 
   * @return array
   */
  public function getColumnsForRow($rowId)
  {
    return DB::table('duty_rows_templ_cols')
      ->where('rowTemplId', $rowId)
      ->get();
  }

  /**
   * get row template for a specific duty row templ id
   * (configurator)
   * 
   * @param $rowId the id of the required row templ
   * 
   * @return object
   */
  public function byId($rowId)
  {
    $q = "
    SELECT dr.id, dr.name, dr.description, dt.id as dutyTriggerId, dr.createOnce, dr.minFinishedTasksToCompl
    FROM duty_row_templates as dr 
    INNER JOIN duty_rows_templ_trigger as drtt ON drtt.dutyRowTemplId = dr.id 
    INNER JOIN duty_triggers as dt ON dt.id = drtt.dutyTriggerId 
    WHERE dr.id = " . $rowId;

    return DB::select($q);
  }

  /**
   * update row order of a specific duty block
   * (configurator)
   * @return array
   */
  public function updateRowOrderForRow($orderedRows, $blockId)
  {
    foreach ($orderedRows as $row) {
      DB::table('duty_blocks_rows_templ')->where('dutyBlockId', '=', $blockId)
        ->where('dutyRowTemplId', '=', $row['id'])
        ->update([
          'pos' => $row['pos']
        ]);
    }
    return;
  }

  /**
   * unlinks a row from a specific duty block
   * and deletes all linked tasks
   * (configurator)
   * 
   * @param $blockId the id of the duty block
   * @param $rowTemplId the id of the row template
   */
  public function unlinkRowFromBlock($blockId, $rowTemplId)
  {
    DB::table('duty_blocks_rows_templ')
      ->where('dutyBlockId', $blockId)
      ->where('dutyRowTemplId', $rowTemplId)
      ->delete();

    DB::table('duty_rows_templ_tasks_templ')
      ->where('dutyBlockId', $blockId)
      ->where('dutyRowId', $rowTemplId)
      ->delete();

    // Also covers rows which are linked in follow ups
    DB::table('duty_follow_ups')
      ->where('dutyBlockId', $blockId)
      ->where(function($q) use ($rowTemplId) {
        $q->where('dutyRowTemplId', $rowTemplId)->orWhere('followUpDutyRowTemplId', $rowTemplId);
      })
      ->delete();
  }
}
