<?php

/**
 * by stephan scheide
 */

namespace App\Services\Core;


use App\Repositories\Core\PermissionRepository;

class PermissionService
{

    private $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * 
     * @return array
     */

    public function allPermissions()
    {
        return $this->permissionRepository->all();
    }

    /**
     * 
     * @return array
     */

    public function allPermissionsOfSpecificType(string $type)
    {
        return $this->permissionRepository->allForSpecificType($type);
    }

    public function getAllPermissionsName()
    {
        return $this->permissionRepository->getSpecificColumn('name');
    }

    /**
     * @return array
     */
    public function userAbsencePermissions()
    {
        return $this->permissionRepository->getUserAbsencePermissions();
    }

    /**
     * @param string $menuName
     * @param string $type
     * @return void
     */

    public function createPermission(string $menuName, string $type, string $prefix = '')
    {
        $menuName = strtolower($menuName);
        $newPermission =  str_replace(' ', '-', strtolower($menuName));
        $newPermission =  $prefix . $newPermission;
        return $this->permissionRepository->createPermission($newPermission, $type);
    }

    /**
     * @param string $menuName
     * @param string $type
     * * @param string $newMenu
     * @return void
     */

    public function updatePermission(string $existingMenuName, string $type, string $newMenu, string $prefix = '')
    {

        $oldPermissionName =  str_replace(' ', '-', strtolower(
            $existingMenuName
        ));
        $oldPermissionName =  $prefix . $existingMenuName;

        $newMenu = strtolower($newMenu);
        $newPermissionName =  str_replace(' ', '-', strtolower(
            $newMenu
        ));
        $newPermissionName =  $prefix . $newPermissionName;

        $this->permissionRepository->updateByName($oldPermissionName, $type, $newPermissionName);
    }

    public function deletePermission(string $menuName, string $type, string $prefix = '')
    {

        $menuName = strtolower($menuName);
        $permissionName =  str_replace(' ', '-', strtolower(
            $menuName
        ));
        $permissionName =  $prefix . $permissionName;

        $this->permissionRepository->deleteByName($permissionName, $type);
    }

    public function findByName(string $permissionName, string $type = null)
    {
        return $this->permissionRepository->findByName($permissionName, $type);
    }
}
