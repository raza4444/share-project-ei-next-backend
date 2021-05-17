<?php

namespace App\Repositories\Core;

use App\Entities\Core\Roles;
use App\Repositories\AbstractRepository;

class RoleRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct(Roles::class);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->query()
            ->get();
    }

    /**
     * @param integer $roleId the role id to return
     * @return array
     */
    public function find($roleId)
    {
        return $this->query()
            ->find($roleId);
    }

    /**
     * @return array
     */
    public function allWithPermissions()
    {
        return $this->query()->with('permissions')->get();
    }

    /**
     * @param integer $roleId the role id
     * @return array all linked permissions for the given role id
     */
    public function allPermissions($roleId)
    {
        return $this->query()->find($roleId)->permissions()->get();
    }

    /**
     * @param integer $roleId the new role object
     * @return array the created role object
     */
    public function create($newRoleObj)
    {
        $newRole = new Roles($newRoleObj);
        $newRole->save();
        return $newRole;
    }

    /**
     * @param integer $roleId the role id to update
     * @param array $roleId the updated role
     * @return array the updated role object
     */
    public function update($roleId, $updatedRole)
    {
        $roleToUpdate = $this->query()->where('id', $roleId)->first();
        $roleToUpdate->name = $updatedRole['name'];
        $roleToUpdate->save();
        return $roleToUpdate;
    }

    /**
     * @param integer $roleId the role id to delete
     */
    public function delete($roleId)
    {
        $this->query()->where('id', $roleId)->delete();
    }

    /**
     * @param integer $roleId the role id to link a permission to
     * @param integer $permissionId the permission id to link with
     * @return array the linked permission object
     */
    public function linkPermission($roleId, $permissionId)
    {
        $this->query()->find($roleId)->permissions()->attach($permissionId);
        return $this->query()->find($roleId)->permissions()->get();
    }

    /**
     * @param integer $roleId the role id
     * @param integer $permissionId the permission id to unlink from
     * @return array the linked permission object
     */
    public function unlinkPermission($roleId, $permissionId)
    {
        $this->query()->find($roleId)->permissions()->detach($permissionId);
        return $this->query()->find($roleId)->permissions()->get();
    }
}
