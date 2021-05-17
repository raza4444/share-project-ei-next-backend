<?php

/**
 * by Samuel Leicht
 */

namespace App\Http\Controllers\Intern\Duties;

use App\Entities\Duties\DutyBlock;
use App\Http\Controllers\AbstractInternController;
use App\Repositories\Duties\DutyBlockRepository;
use App\Core\Entity\PermissionType;
use Illuminate\Http\Request;
use App\Services\Core\PermissionService;

class DutyBlockController extends AbstractInternController
{

  private $dutyBlockRepository;
  private $permissionService;

  public function __construct(
    DutyBlockRepository $locationAppointmentRepository,
    PermissionService $permissionService
  ) {
    $this->dutyBlockRepository = $locationAppointmentRepository;
    $this->permissionService = $permissionService;
  }

  public function create(Request $request)
  {
    $block = $this->jsonAsEntity($request, DutyBlock::class);
    $this->permissionService->createPermission($request->get('name'), 'duty-configurator-block', 'aufgabenbloecke-');
    $block->save();
    return $this->entityCreated($block);
  }

  public function updateBlockOrder(Request $request)
  {
    $all = $request->json()->all();
    $this->dutyBlockRepository->updateRowOrderForRow($all);
    return $this->noContent();
  }

  public function getAll()
  {
    return $this->dutyBlockRepository->getAll();
  }

  public function getForId($id)
  {
    return $this->dutyBlockRepository->byId($id);
  }

  public function delete($id)
  {
    $block = $this->dutyBlockRepository->byId($id);
    $this->permissionService->deletePermission($block->name , 'duty-configurator-block', 'aufgabenbloecke-');
    $this->dutyBlockRepository->deleteById($id);
    return $this->noContent();
  }

  public function updatePartial(Request $request, $id)
  {
    $changeAbleKeys = ['name'];
    $all = $request->json()->all();
    $block = $this->dutyBlockRepository->byId($id);
    if ($request->get('name') != $block->name) {
      $this->permissionService->updatePermission($block->name,  'duty-configurator-block', $request->get('name'), 'aufgabenbloecke-');
    }
    $changed = false;

    foreach ($changeAbleKeys as $key) {
      if (array_key_exists($key, $all)) {
        $block->$key = $all[$key];
        $changed = true;
      }
    }

    if ($changed) {
      $block->save();
    }

    return $this->dutyBlockRepository->byId($id);
  }
}
