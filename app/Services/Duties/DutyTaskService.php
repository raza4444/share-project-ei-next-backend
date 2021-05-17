<?php

/**
 * by Samuel Leicht
 */

namespace App\Services\Duties;

use App\Repositories\Duties\DutyTaskRepository;
use Illuminate\Support\Facades\DB;

class DutyTaskService
{
  private $dutyTaskRepository;

  public function __construct(
    DutyTaskRepository $dutyTaskRepository
  ) {
    $this->dutyTaskRepository = $dutyTaskRepository;
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
    return $this->dutyTaskRepository->findAllDoneForLocation($locationId);
  }

  /**
   * returns all duty tasks for a specific duty row
   *
   * @param $rowId the id of the duty row
   * 
   * @return array
   */
  public function dutyTasksForDutyRow($rowId)
  {
    return DB::table('duty_rows_tasks AS drt')
      ->where('drt.dutyRowId', $rowId)
      ->join('duty_rows AS dr', 'dr.id', 'drt.dutyRowId')
      ->join('duty_task_templates AS dtt', 'dtt.id', 'drt.dutyTaskTemplId')
      ->join('duty_rows_templ_tasks_templ AS drttt', function($q) {
        $q->on('drttt.dutyTaskId', 'drt.dutyTaskTemplId');
        $q->on('drttt.dutyRowId', 'dr.dutyRowTemplId');
        $q->on('drttt.dutyBlockId', 'dr.dutyBlockId');
      })
      ->orderBy('drttt.pos', 'asc')
      ->get(array(
        'drt.id',
        'dtt.name',
        'dtt.description',
        'dtt.createOnce',
        'drt.id',
        'drt.done',
        'drt.updated_at'
      ));
  }

  /**
   * creates a new tasks for a follow up in all rows of block/row combination
   *
   * @param $taskTemplId the id of the duty task template
   * @param $targetDutyRowId the id of the target duty row
   * @param $dutyBlocksRowsTemplId the id of the blocks-rows-template combination
   * 
   * @return boolean if a new task was created
   */
  public function createTaskInRowsForFollowUp($dutyRowId, $taskTemplId, $targetDutyBlockId, $dutyBlocksRowsTemplId)
  {
    $q = '
    INSERT IGNORE INTO duty_rows_tasks (dutyTaskTemplId, dutyRowId)
    SELECT ' . $taskTemplId . ', dr.id FROM duty_rows AS dr 
    INNER JOIN duty_blocks_rows_templ AS dbrt ON dbrt.id = ' . $dutyBlocksRowsTemplId . '
    INNER JOIN duty_task_templates AS dtt ON dtt.id = ' . $taskTemplId . '
    AND dr.dutyRowTemplId = dbrt.dutyRowTemplId
    AND dr.dutyBlockId = ' . $targetDutyBlockId . '
    AND dr.done = 0
    AND CASE dtt.createOnce WHEN 1 THEN
    (SELECT COUNT(drt2.id) FROM duty_rows_tasks AS drt2
    INNER JOIN duty_rows AS dr2 on dr2.id = drt2.dutyRowId
    INNER JOIN duty_task_templates AS dtt2 on dtt2.id = drt2.dutyTaskTemplId AND dtt2.id = ' . $taskTemplId . '
    WHERE dtt2.id = ' . $taskTemplId . '
    AND dr2.locationId = (SELECT locationId FROM duty_rows WHERE id = ' . $dutyRowId . ')
    AND drt2.done = 1) = 0
    ELSE 1 END';

    DB::insert($q);

    if (DB::getPdo()->lastInsertId() > 0) {
      return true;
    }

    return false;
  }

  /**
   * creates a new tasks for a follow up in specific row
   *
   * @param $dutyRowId the id of the specific duty row
   * @param $taskTemplId the id of the duty task template
   * 
   * @return boolean if a new task was created
   */
  public function createTaskForFollowUp($dutyRowId, $taskTemplId)
  {
    $q = '
    INSERT IGNORE INTO duty_rows_tasks (dutyRowId, dutyTaskTemplId)
    SELECT ' . $dutyRowId . ', ' . $taskTemplId . ' WHERE
    (SELECT dtt.createOnce FROM duty_task_templates AS dtt WHERE dtt.id = ' . $taskTemplId . '
    AND CASE dtt.createOnce WHEN 1 THEN
    (SELECT COUNT(drt2.id) FROM duty_rows_tasks AS drt2
    INNER JOIN duty_rows AS dr2 ON dr2.id = drt2.dutyRowId
    INNER JOIN duty_task_templates AS dtt2 on dtt2.id = drt2.dutyTaskTemplId AND dtt2.id = ' . $taskTemplId . '
    WHERE dtt2.id = ' . $taskTemplId . '
    AND dr2.locationId = (SELECT locationId FROM duty_rows WHERE id = ' . $dutyRowId . ')
    AND drt2.done = 1) = 0
    ELSE 1 END)';

    DB::insert($q);

    if (DB::getPdo()->lastInsertId() > 0) {
      return true;
    }

    return false;
  }

  /**
   * creates all tasks for follow up row
   * 
   * @param $rowId the newly created row id
   */
  public function createTasksForFollowUpRow($dutyRowId)
  {

    $q = '
      INSERT INTO duty_rows_tasks (dutyRowId, dutyTaskTemplId)
      SELECT dr.id, drttt.dutyTaskId
      FROM duty_rows as dr
      INNER JOIN duty_rows_templ_tasks_templ as drttt ON drttt.dutyRowId = dr.dutyRowTemplId
      AND drttt.dutyBlockId = dr.dutyBlockId
      INNER JOIN duty_task_templates AS dtt ON dtt.id = drttt.dutyTaskId
      WHERE dr.id = ' . $dutyRowId . '
      AND CASE dtt.createOnce WHEN 1 THEN
      (SELECT COUNT(drt2.id) FROM duty_rows_tasks AS drt2
      INNER JOIN duty_rows AS dr2 ON dr2.id = drt2.dutyRowId
      WHERE drt2.dutyTaskTemplId = drttt.dutyTaskId
      AND dr2.locationId = (SELECT locationId FROM duty_rows WHERE id = ' . $dutyRowId . ')
      AND drt2.done = 1) = 0
      ELSE 1 END';
    
    DB::insert($q);
  }

  /**
   * creates all tasks for follow up row of a new appointment
   * 
   * @param $appId the id of the appointment
   */
  public function createTasksForFollowUpRowForAppointment($appId, $locId)
  {

    // Select all tasks which need to get inserted for new appointment row
    $tasksToInsert = DB::select('SELECT dr.id, drttt.dutyTaskId, dtt.createOnce
    FROM duty_rows as dr
    INNER JOIN duty_rows_templ_tasks_templ as drttt
    ON drttt.dutyRowId = dr.dutyRowTemplId
    AND drttt.dutyBlockId = dr.dutyBlockId
    INNER JOIN duty_task_templates as dtt
    ON dtt.id = drttt.dutyTaskId
    WHERE dr.appointmentId = ' . $appId);

    foreach ($tasksToInsert as $taskToInsert) {

      // If task should only get created once ...
      if ($taskToInsert->createOnce === 1) {

        // ... check if there are already existing, finished tasks of that kind
        $existingTasks = DB::select('SELECT drt.id FROM duty_rows_tasks AS drt
        INNER JOIN duty_rows AS dr ON dr.id = drt.dutyRowId 
        AND dr.locationId = ' . $locId . ' 
        AND drt.dutyTaskTemplId = ' . $taskToInsert->dutyTaskId . '
        AND drt.done = 1');

        // If there are none ...
        if (count($existingTasks) === 0) {

          // ... insert it
          DB::insert('INSERT INTO duty_rows_tasks (dutyRowId, dutyTaskTemplId)
          VALUES (' . $taskToInsert->id . ', ' . $taskToInsert->dutyTaskId . ')');
        }
      } else { // If task should NOT only created once ...

        // ... just insert it
        DB::insert('INSERT INTO duty_rows_tasks (dutyRowId, dutyTaskTemplId)
          VALUES (' . $taskToInsert->id . ', ' . $taskToInsert->dutyTaskId . ')');
      }
    }
  }
}
