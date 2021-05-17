<?php

/**
 * by Samuel Leicht
 */

namespace App\Http\Controllers\Intern\Duties;

use App\Entities\Duties\DutyTaskTempl;
use App\Http\Controllers\AbstractInternController;
use App\Repositories\Duties\DutyTaskTemplRepository;
use App\Services\Duties\DutyTaskTemplService;
use Illuminate\Http\Request;

class DutyTaskTemplController extends AbstractInternController
{
  private $dutyTaskTemplService;
  private $dutyTaskTemplRepository;

  public function __construct(
    DutyTaskTemplService $dutyTaskTemplService,
    DutyTaskTemplRepository $dutyTaskTemplRepository
  ) {
    $this->dutyTaskTemplService = $dutyTaskTemplService;
    $this->dutyTaskTemplRepository = $dutyTaskTemplRepository;
  }

  // Configuratior
  public function create(Request $request)
  {
    $newObj = $this->jsonAsEntity($request, DutyTaskTempl::class);
    $newObj->save();
    return $this->entityCreated($newObj);
  }

  // Configuratior
  public function getAll()
  {
    $tasks = $this->dutyTaskTemplRepository->getAll();
    return $this->singleJson($tasks);
  }

  // Configuratior
  public function getAllTemplatesForRow($blockId, $rowId)
  {
    $tasks = $this->dutyTaskTemplService->dutyTaskTemplatesForDutyRow($blockId, $rowId);
    return $this->singleJson($tasks);
  }

  // Configuratior
  public function linkTaskToRow(Request $request, $blockId, $rowId, $taskId)
  {
    $body = $request->json()->all();
    $this->dutyTaskTemplService->linkTaskToRow($blockId, $rowId, $taskId, $body["pos"]);
    return $this->noContent();
  }

  // Configuratior
  public function updateTaskOrderForRow(Request $request, $blockId, $rowId)
  {
    $all = $request->json()->all();
    $this->dutyTaskTemplService->updateTaskOrderForRow($all, $blockId, $rowId);
    return $this->noContent();
  }

  // Configuratior
  public function unlinkTaskFromRow($blockId, $rowId, $taskId)
  {
    $this->dutyTaskTemplService->unlinkTaskFromRow($blockId, $rowId, $taskId);
    return $this->noContent();
  }

  // Configuratior
  public function delete($id)
  {
    $this->dutyTaskTemplRepository->deleteById($id);
    return $this->noContent();
  }

  // Configuratior
  public function updatePartial(Request $request, $taskId)
  {
    // First update task template
    $changeAbleKeys = ['name', 'description', 'createOnce'];
    $all = $request->json()->all();
    $task = $this->dutyTaskTemplRepository->byId($taskId);
    $changed = false;

    foreach ($changeAbleKeys as $key) {
      if (array_key_exists($key, $all)) {
        $task->$key = $all[$key];
        $changed = true;
      }
    }

    if ($changed) {
      $task->save();
    }

    return $this->dutyTaskTemplRepository->byId($taskId);
  }
}
