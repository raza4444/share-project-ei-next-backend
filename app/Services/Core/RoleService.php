<?php

/**
 * by stephan scheide
 */

namespace App\Services\Core;


use App\Repositories\Core\RoleRepository;

class RoleService
{

    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function all()
    {
        return $this->roleRepository->all();
    }

    public function find($roleId)
    {
        return $this->roleRepository->find($roleId);
    }

    public function allWithPermissions()
    {
        return $this->roleRepository->allWithPermissions();
    }

    public function allPermissions($roleId)
    {
        return $this->roleRepository->allPermissions($roleId);
    }

    public function create($newRoleObj)
    {
        return $this->roleRepository->create($newRoleObj);
    }

    public function update($roleId, $updatedRoleObj)
    {
        return $this->roleRepository->update($roleId, $updatedRoleObj);
    }

    public function delete($roleId)
    {
        $this->roleRepository->delete($roleId);
    }

    public function linkPermission($roleId, $permissionId)
    {
        return $this->roleRepository->linkPermission($roleId, $permissionId);
    }

    public function unlinkPermission($roleId, $permissionId)
    {
        return $this->roleRepository->unlinkPermission($roleId, $permissionId);
    }
}
