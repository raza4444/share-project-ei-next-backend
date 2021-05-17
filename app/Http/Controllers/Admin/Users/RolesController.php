<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\AbstractInternController;
use App\Services\Core\RoleService;
use Illuminate\Http\Request;

class RolesController extends AbstractInternController
{
  private $roleService;

  public function __construct(RoleService $roleService)
  {
    $this->roleService = $roleService;
  }

  public function all()
  {
    return $this->roleService->all();
  }

  public function allWithPermissions()
  {
    return $this->roleService->allWithPermissions();
  }

  public function allPermissions($roleId)
  {
    return $this->roleService->allPermissions($roleId);
  }

  public function create(Request $request)
  {
    // Validation missing
    return $this->roleService->create($request->all());
  }

  public function update(Request $request, $id)
  {
    // Validation missing
    return $this->roleService->update($id, $request->all());
  }

  public function delete($id)
  {
    $this->roleService->delete($id);
    return $this->noContent();
  }

  public function linkPermission(Request $request, $roleId)
  {
    $permissionId = $request->all()['permissionId'];
    if ($permissionId) {
      return $this->roleService->linkPermission($roleId, $permissionId);
    } else {
      return $this->badRequest();
    }
  }

  public function unlinkPermission($roleId, $permissionId)
  {
    return $this->roleService->unlinkPermission($roleId, $permissionId);
  }
}
