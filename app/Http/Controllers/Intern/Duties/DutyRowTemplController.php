<?php

/**
 * by Samuel Leicht
 */

namespace App\Http\Controllers\Intern\Duties;

use App\Entities\Duties\DutyBlockRowTempl;
use App\Entities\Duties\DutyRowTempl;
use App\Entities\Duties\DutyRowTemplColumn;
use App\Entities\Duties\DutyRowTemplTrigger;
use App\Http\Controllers\AbstractInternController;
use App\Repositories\Duties\DutyRowTemplColRepository;
use App\Repositories\Duties\DutyRowTemplRepository;
use App\Repositories\Duties\DutyRowTemplTriggerRepository;
use App\Services\Duties\DutyRowTemplService;
use Illuminate\Http\Request;

class DutyRowTemplController extends AbstractInternController
{
  private $dutyRowTemplService;
  private $dutyRowTemplRepository;
  private $dutyRowTemplTriggerRepository;
  private $dutyRowTemplColRepository;

  public function __construct(
    DutyRowTemplService $dutyRowTemplService,
    DutyRowTemplRepository $dutyRowTemplRepository,
    DutyRowTemplTriggerRepository $dutyRowTemplTriggerRepository,
    DutyRowTemplColRepository $dutyRowTemplColRepository
  ) {
    $this->dutyRowTemplService = $dutyRowTemplService;
    $this->dutyRowTemplRepository = $dutyRowTemplRepository;
    $this->dutyRowTemplTriggerRepository = $dutyRowTemplTriggerRepository;
    $this->dutyRowTemplColRepository = $dutyRowTemplColRepository;
  }

  public function create(Request $request, $blockId)
  {
    $newRow = $this->jsonAsEntity($request, DutyRowTempl::class);
    $newRow->save();

    $newBlockRowTempl = new DutyBlockRowTempl();
    $newBlockRowTempl->dutyBlockId = $blockId;
    $newBlockRowTempl->dutyRowTemplId = $newRow->id;
    $newBlockRowTempl->pos = $request["pos"];
    $newBlockRowTempl->save();

    $newRowTemplTrigger = new DutyRowTemplTrigger();
    // $newRowTemplTrigger->dutyBlockId = $blockId;
    $newRowTemplTrigger->dutyRowTemplId = $newRow->id;
    $newRowTemplTrigger->dutyTriggerId = $request["dutyTriggerId"];
    $newRowTemplTrigger->save();

    return $this->singleJson($newRow->id);
  }

  public function getAll()
  {
    $rows = $this->dutyRowTemplRepository->getAll();
    return $this->singleJson($rows);
  }

  public function getAllForBlock($blockId)
  {
    $rows = $this->dutyRowTemplService->getAllForBlock($blockId);
    return $this->singleJson($rows);
  }

  public function getColumnsForRow($rowId)
  {
    $columns = $this->dutyRowTemplService->getColumnsForRow($rowId);
    return $this->singleJson($columns);
  }

  public function addColumnsToRow(Request $request, $rowId)
  {
    $cols = $request->json()->all();

    foreach ($cols as $col) {
      $newRowTemplCol = new DutyRowTemplColumn();
      $newRowTemplCol->rowTemplId = $rowId;
      $newRowTemplCol->columnId = $col["columnId"];
      $newRowTemplCol->colDateTypeId = $col["colDateTypeId"];
      $newRowTemplCol->colValueId = $col["colValueId"];

      if (array_key_exists("colDateOffset", $col)) {
        $newRowTemplCol->colDateOffset = $col["colDateOffset"];
      }

      if (array_key_exists("colDateTypeId", $col)) {
        $newRowTemplCol->colDateTypeId = $col["colDateTypeId"];
      }

      $newRowTemplCol->save();
    }

    return $this->created();
  }

  public function getAllInclDataForBlock($blockId)
  {
    $rows = $this->dutyRowTemplService->getAllForBlock($blockId);
    return $this->singleJson($rows);
  }

  public function delete($rowId)
  {
    $this->dutyRowTemplRepository->deleteById($rowId);
    return $this->noContent();
  }

  public function linkRowToBlock(Request $request, $blockId, $rowId)
  {
    // DutyBlockRowTempl
    $newBlockRowTempl = $this->jsonAsEntity($request, DutyBlockRowTempl::class);
    $newBlockRowTempl->dutyBlockId = $blockId;
    $newBlockRowTempl->dutyRowTemplId = $rowId;
    $newBlockRowTempl->save();

    // DutyRowTemplTrigger
    // $newRowTemplTrigger = new DutyRowTemplTrigger();
    // $newRowTemplTrigger->dutyBlockId = $blockId;
    // $newRowTemplTrigger->dutyRowTemplId = $rowId;
    // $newRowTemplTrigger->save();
    
    return $this->noContent();
  }

  public function unlinkRowFromBlock($blockId, $rowId)
  {
    $this->dutyRowTemplService->unlinkRowFromBlock($blockId, $rowId);
    return $this->noContent();
  }

  public function updateRowOrderForRow(Request $request, $blockId)
  {
    $all = $request->json()->all();
    $this->dutyRowTemplService->updateRowOrderForRow($all, $blockId);
    return $this->noContent();
  }

  public function updateColumnsPartial(Request $request, $rowTemplId)
  {
    $this->dutyRowTemplColRepository->deleteAllColumnsOfRowTempl($rowTemplId);
    $cols = $request->json()->all();
    foreach ($cols as $col) {
      $dbCol = new DutyRowTemplColumn();
      $dbCol->rowTemplId = $rowTemplId;
      $dbCol->columnId = $col["columnId"];
      $dbCol->colDateTypeId = $col["colDateTypeId"];
      $dbCol->colDateOffset = $col["colDateOffset"];
      $dbCol->colValueId = $col["colValueId"];
      $dbCol->save();
    }
  }

  public function updatePartial(Request $request, $rowId)
  {
    // First update row itself
    $changeAbleKeys = ['name', 'description', 'createOnce', 'minFinishedTasksToCompl'];
    $all = $request->json()->all();
    $row = $this->dutyRowTemplRepository->byId($rowId);
    $changed = false;

    foreach ($changeAbleKeys as $key) {
      if (array_key_exists($key, $all)) {
        $row->$key = $all[$key];
        $changed = true;
      }
    }

    if ($changed) {
      $row->save();
    }

    // Now update linked row trigger
    $row_trigger = $this->dutyRowTemplTriggerRepository->findByRowTemplId($rowId);
    $row_trigger->dutyTriggerId = $all["dutyTriggerId"];
    $row_trigger->save();

    // Return updated row
    return $this->singleJson($this->dutyRowTemplService->byId($rowId)[0]);
  }
}
