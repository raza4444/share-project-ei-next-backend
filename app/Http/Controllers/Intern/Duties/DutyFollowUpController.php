<?php

/**
 * by Samuel Leicht
 */

namespace App\Http\Controllers\Intern\Duties;

use App\Entities\Duties\DutyFollowUp;
use App\Http\Controllers\AbstractInternController;
use App\Repositories\Duties\DutyBlockRowTemplRepository;
use App\Repositories\Duties\DutyFollowUpRepository;
use App\Repositories\Duties\DutyRowRepository;
use App\Repositories\Duties\DutyRowTemplRepository;
use App\Services\Duties\DutyRowService;
use App\Services\Duties\DutyTaskService;
use Illuminate\Http\Request;

class DutyFollowUpController extends AbstractInternController
{
  private $dutyFollowUpRepository;
  private $dutyRowTemplRepository;
  private $dutyBlockRowTemplRepository;
  private $dutyTaskService;
  private $dutyRowService;

  public function __construct(
    DutyFollowUpRepository $dutyFollowUpRepository,
    DutyRowTemplRepository $dutyRowTemplRepository,
    DutyRowRepository $dutyRowRepository,
    DutyBlockRowTemplRepository $dutyBlockRowTemplRepository,
    DutyTaskService $dutyTaskService,
    DutyRowService $dutyRowService
  ) {
    $this->dutyFollowUpRepository = $dutyFollowUpRepository;
    $this->dutyRowTemplRepository = $dutyRowTemplRepository;
    $this->dutyTaskService = $dutyTaskService;
    $this->dutyBlockRowTemplRepository = $dutyBlockRowTemplRepository;
    $this->dutyRowService = $dutyRowService;
    $this->dutyRowRepository = $dutyRowRepository;
  }

  public function create(Request $request, $blockId, $rowId, $taskId)
  {
    if ($this->dutyRowTemplRepository->taskTemplExists($blockId, $rowId, $taskId)) {

      $followUp = $this->jsonAsEntity($request, DutyFollowUp::class);

      $followUp->dutyBlockId = $blockId;
      $followUp->dutyRowTemplId = $rowId;
      $followUp->dutyTaskTemplId = $taskId;
      $followUp->targetDutyBlockRowTemplId = $this->dutyBlockRowTemplRepository
        ->getIdForBlockRowCombination($request->all()["targetDutyBlockId"], $request->all()["targetDutyRowTemplId"]);
      $followUp->sameRowCreation = $request->all()["sameRowCreation"];

      $followUp->save();
      return $this->entityCreated($followUp);
    } else {
      return $this->notFound();
    }
  }

  public function getFollowUpsForTaskTempl($blockId, $rowId, $taskId)
  {
    return $this->singleJson($this->dutyFollowUpRepository->getFollowUpsForTaskTempl($blockId, $rowId, $taskId));
  }

  public function deleteFollowUp($followUpId)
  {
    if ($this->dutyFollowUpRepository->deleteFollowUp($followUpId) === 1) {
      return $this->noContent();
    }
    return $this->notFound();
  }

  public function updatePartial(Request $request, $followUpId)
  {
    $changeAbleKeys = ['followUpDutyTaskTemplId', 'followUpDutyRowTemplId', 'targetDutyBlockId', 'sameRowCreation', 'interactionMsg', 'followUpInteractionTypeId'];
    $all = $request->json()->all();

    $followUp = $this->dutyFollowUpRepository->getRawFollowUp($followUpId);

    if ($followUp !== null) {

      foreach ($changeAbleKeys as $key) {
        if (array_key_exists($key, $all)) {
          $followUp->$key = $all[$key];
        }
      }

      // Handle targetDutyBlockRowTemplId seperately due to linked table duty_blocks_rows_templ
      if (isset($all['targetDutyRowTemplId'])) {
        $followUp->targetDutyBlockRowTemplId = $this->dutyBlockRowTemplRepository
          ->getIdForBlockRowCombination($all['targetDutyBlockId'], $all['targetDutyRowTemplId']);
      }

      $followUp->save();

      // This field must be maintained in order to avoid front-end issues
      $followUp->targetDutyRowTemplId = $all['targetDutyRowTemplId'];

      return $this->singleJson($followUp);
    } else {
      return $this->notFound();
    }
  }

  public function handleFollowUp(Request $request, $rowId, $followUpId)
  {
    $followUp = $this->dutyFollowUpRepository->byId($followUpId);

    if (isset($followUp->followUpDutyRowTemplId)) { // Follow Up: Create Duty Row

      $newRow = $this->dutyRowService->createFollowUpRow(
        $rowId,
        $followUp->targetDutyBlockId,
        $followUp->followUpDutyRowTemplId
      );

      // If a new row was created, create all linked tasks as well
      if (isset($newRow)) {
        $this->dutyTaskService->createTasksForFollowUpRow($newRow->id);
      }

      // Delete old duplicates
      $this->dutyRowService->finishOldDuplicateRowsForLocationForNewApp(null);

    } else if (isset($followUp->followUpDutyTaskTemplId)) { // Follow Up: Create Duty Task

      if ($followUp->sameRowCreation === 0) {

        $newTaskCreated = $this->dutyTaskService->createTaskInRowsForFollowUp(
          $rowId,
          $followUp->followUpDutyTaskTemplId,
          $followUp->targetDutyBlockId,
          $followUp->targetDutyBlockRowTemplId
        );

        // Only send 201 to trigger reload if one of created tasks belongs to same row
        $row = $this->dutyRowRepository->byId($rowId);
        $targetDutyRowTemplId = $this->dutyBlockRowTemplRepository->byId($followUp->targetDutyBlockRowTemplId);

        if ($newTaskCreated && $row->dutyRowTemplId === $targetDutyRowTemplId->dutyRowTemplId) {
          return $this->created();
        }
      } else {

        $newTaskCreated = $this->dutyTaskService->createTaskForFollowUp(
          $rowId,
          $followUp->followUpDutyTaskTemplId
        );

        // Always send 201 since it's the same row
        if ($newTaskCreated) {
          return $this->created();
        }
      }
    }
  }
}
