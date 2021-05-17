<?php

/**
 * by Samuel Leicht
 */

namespace App\Http\Controllers\Intern\Duties;

use App\Entities\Duties\DutyTask;
use App\Http\Controllers\AbstractInternController;
use App\Repositories\Duties\DutyRowRepository;
use App\Repositories\Duties\DutyTaskRepository;
use App\Services\Duties\DutyFollowUpService;
use App\Services\Duties\DutyRowService;
use App\Services\Duties\DutyTaskService;
use Illuminate\Http\Request;

class DutyTaskController extends AbstractInternController
{
  private $dutyTaskService;
  private $dutyTaskRepository;
  private $dutyRowRepository;
  private $dutyFollowUpService;

  public function __construct(
    DutyTaskRepository $dutyTaskRepository,
    DutyRowRepository $dutyRowRepository,
    DutyFollowUpService $dutyFollowUpService,
    DutyRowService $dutyRowService,
    DutyTaskService $dutyTaskService
  ) {
    $this->dutyTaskRepository = $dutyTaskRepository;
    $this->dutyRowRepository = $dutyRowRepository;
    $this->dutyFollowUpService = $dutyFollowUpService;
    $this->dutyRowService = $dutyRowService;
    $this->dutyTaskService = $dutyTaskService;
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
    return $this->dutyTaskService->findAllDoneForLocation($locationId);
  }

  public function createTask($dutyRowId, $dutyTaskTemplId)
  {
    $newTask = new DutyTask();
    $newTask->dutyRowId = $dutyRowId;
    $newTask->dutyTaskTemplId = $dutyTaskTemplId;
    $newTask->save();
  }

  public function getAllForRow($rowId)
  {
    $tasks = $this->dutyTaskService->dutyTasksForDutyRow($rowId);

    foreach ($tasks as $task) {
      $task->follow_ups = $this->dutyFollowUpService->getFollowUpsForTask($task->id);
    }

    return $this->singleJson($tasks);
  }

  public function updateStatus(Request $request, $rowId, $taskId)
  {
    $all = $request->json()->all();
    $this->dutyTaskRepository->updateStatus($taskId, $all['updatedTask']['done'], $this->getCurrentUserId());
    $this->dutyRowRepository->updateUpdatedBy($rowId, $this->getCurrentUserId());
  }

  public function finishedTodayTasks()
  {
    return $this->singleJson($this->dutyTaskRepository->finishedTodayTasks());
  }

  public function finishedYesterdayTasks()
  {
    return $this->singleJson($this->dutyTaskRepository->finishedYesterdayTasks());
  }

  public function finishedLastMonthTasks()
  {
    return $this->singleJson($this->dutyTaskRepository->finishedLastMonthTasks());
  }
 
}
