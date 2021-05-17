<?php

namespace App\Http\Controllers\Admin\Users;

use App\Entities\Core\PermissionType;
use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;

class PermissionTypeController extends AbstractInternController
{

  public function all()
  {
    return PermissionType::get();
  }

  public function create(Request $request)
  {
    $newPermission = PermissionType::create([
      'name' => $request->all()['name']
    ]);
    return $newPermission;
  }

  public function update(Request $request, $id)
  {
    $permissionToUpdate = PermissionType::where('id', $id)->first();
    $permissionToUpdate->name = $request->all()['name'];
    $permissionToUpdate->save();
    return $permissionToUpdate;
  }

  public function delete($id)
  {
    PermissionType::where('id', $id)->delete();
    return $this->noContent();
  }
}
