<?php

namespace App\Http\Controllers\Admin\Users;

use App\Entities\Core\Permissions;
use App\Http\Controllers\AbstractInternController;
use App\Services\Core\PermissionService;
use Illuminate\Http\Request;

class PermissionsController extends AbstractInternController
{
  
  private $permissionService;

  public function __construct(
    PermissionService $permissionService
  ) {
    $this->permissionService = $permissionService;
  }

  /**
   * @return array | null
   */
  public function all()
  {
    return $this->permissionService->allPermissions();
  }

    /**
   * @return array | null
   */
  public function allWithSpecificType($type)
  {
    return $this->permissionService->allPermissionsOfSpecificType($type);
  }
  /**
   * 
   * @return array | null
   */
  public function onlyName()
  {
    return $this->permissionService->getAllPermissionsName();
  }

  public function create(Request $request)
  {
    $newPermission = new Permissions($request->all());
    $newPermission->save();
    return $newPermission;
  }

  public function update(Request $request, $id)
  {
    $permissionToUpdate = Permissions::where('id', $id)->first();
    $permissionToUpdate->name = $request->all()['name'];
    $permissionToUpdate->save();
    return $permissionToUpdate;
  }

  public function delete($id)
  {
    Permissions::where('id', $id)->delete();
    return $this->noContent();
  }
}
