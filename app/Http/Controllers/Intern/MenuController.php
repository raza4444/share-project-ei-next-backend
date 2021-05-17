<?php

/**
 * Created by PhpStorm.
 * User: kingster
 * Date: 16.12.2018
 * Time: 16:59
 */

namespace App\Http\Controllers\Intern;


use App\Entities\Menus\Menu;
use App\Entities\Menus\MenuItem;
use App\Http\Controllers\AbstractInternController;
use App\Services\Duties\DutyBlockService;
use App\Services\Core\PermissionService;
use App\Utils\StringUtils;

class MenuController extends AbstractInternController
{
  private $dutyBlockService;
  private $permissionService;

  public function __construct(
    DutyBlockService $dutyBlockService,
    PermissionService $permissionService
  ) {
    $this->dutyBlockService = $dutyBlockService;
    $this->permissionService = $permissionService;
  }


  public function menuStructure()
  {

    $menus = Menu::with('items')->with('permissions')->orderBy('nummer', 'asc')->get();

    $user = $this->getCurrentUser();

    $userArray = $this->getCurrentUser()->toArray();
    $isAdmin = $user['admin'];
    $rolePermissions = $userArray['roles']['permissions'];
    $individualPermissions = $userArray['individual_permissions'];
    $allUserPermissions = array_unique(array_merge($rolePermissions, $individualPermissions), SORT_REGULAR);
    $allUserPermissionsName = [];
    foreach ($allUserPermissions as $permission) {
      $allUserPermissionsName[] = $permission['name'];
    }
    //return json_encode($allUserPermissionsName);


    $result = ['menus' => []];
    /**
     * @var Menu $menu
     * @var MenuItem $item
     */
    foreach ($menus as $menu) {

      if (($isAdmin && $isAdmin == 1) || ($isAdmin != 1 && $this->checkPermissionOfMenu($allUserPermissionsName, $menu))) {
        if ($menu->sichtbar !== 1) continue;

        $items = $menu->items;
        $added = false;
        $menuTo = null;

        // Regular menu items
        foreach ($items as $item) {

          if (StringUtils::isEmpty($item->schluessel)) {
            $this->debug('Ignoriere Menu ' . $item->titel);
          }


          if ($item->sichtbar === 1) {


            if (!$added) {
              $menuTo = $this->createMenuTO($menu);
              $added = true;
            }
            $menuTo['items'][] = $item;
          }
        }

        // Duty list
        if ($menu->schluessel === 'a') {

          $blocks = $this->receiveTasksMenuItems($isAdmin, $allUserPermissionsName, false);

          foreach ($blocks as $block) {

            if (!$added) {
              $menuTo = $this->createMenuTO($menu);
              $added = true;
            }

            $menuTo['items'][] = $block;
          }
        }

        if ($menuTo != null) {
          $result['menus'][] = $menuTo;
        }
      } else {
        if ($menu->sichtbar !== 1) continue;
        $items = $menu->items;
        $added = false;
        $menuTo = null;

        // Regular menu items
        foreach ($items as $item) {

          if (StringUtils::isEmpty($item->schluessel)) {
            $this->debug('Ignoriere Menu ' . $item->titel);
          }

          if ($item->sichtbar === 1) {

            if (($isAdmin && $isAdmin == 1) || ($isAdmin != 1 && $this->checkPermissionOfMenu($allUserPermissionsName, $item))) {

              if (!$added) {
                $menuTo = $this->createMenuTO($menu);
                $added = true;
              }

              $menuTo['items'][] = $item;
            }
          }
        }

        // Duty list
        if ($menu->schluessel === 'a') {
          $blocks = $this->receiveTasksMenuItems($isAdmin, $allUserPermissionsName, true);

          foreach ($blocks as $block) {
            if (!$added) {
              $menuTo = $this->createMenuTO($menu);
              $added = true;
            }
            $menuTo['items'][] = $block;
          }
        }

        if ($menuTo != null) {
          $result['menus'][] = $menuTo;
        }
      }
    }

    return $this->singleJson($result);
  }


  private function checkPermissionOfMenu($allUserPermissionsName, $menu)
  {
    $permissionName = (isset($menu->permissions) &&  isset($menu->permissions->name)) ?  $menu->permissions->name : null;
    return (in_array($permissionName, $allUserPermissionsName));
  }

  private function receiveTasksMenuItems($isAdmin, $allUserPermissions, $withoutPermissionCheck)
  {

    $dutyBlocks = $this->dutyBlockService->getAllWithRowCount();
    $items = [];

    for ($i = 0; $i < sizeof($dutyBlocks); $i++) {

      $dutyBlockNam = strtolower($dutyBlocks[$i]->name);
      $dutyBlockPermission =  str_replace(' ', '-', strtolower(
        $dutyBlockNam
      ));
      $dutyBlockPermission =  'aufgabenbloecke-' . $dutyBlockPermission;

      if ($withoutPermissionCheck) {
        if (($isAdmin && $isAdmin == 1) || ($isAdmin != 1 && in_array($dutyBlockPermission, $allUserPermissions))) {
          $items[] = [
            'id' => $dutyBlocks[$i]->id,
            'schluessel' => 'a' . ($i + 1),
            'nummer' => $i + 1,
            'routerlink' => 'duty-list/blocks/' . $dutyBlocks[$i]->id,
            'titel' => $dutyBlocks[$i]->name,
            'badgeCount' => $dutyBlocks[$i]->rowCount,
            'permissions' => $this->permissionService->findByName($dutyBlockPermission, 'duty-configurator-block'),
          ];
        }
      } else {
        $items[] = [
          'id' => $dutyBlocks[$i]->id,
          'schluessel' => 'a' . ($i + 1),
          'nummer' => $i + 1,
          'routerlink' => 'duty-list/blocks/' . $dutyBlocks[$i]->id,
          'titel' => $dutyBlocks[$i]->name,
          'badgeCount' => $dutyBlocks[$i]->rowCount,
          'permissions' => $this->permissionService->findByName($dutyBlockPermission, 'duty-configurator-block'),
        ];
      }
    }

    return $items;
  }

  private function createMenuTO(Menu $menu)
  {
    return ['id' => $menu->id, 'titel' => $menu->titel, 'nummer' => $menu->nummer, 'schluessel' => $menu->schluessel, 'items' => []];
  }
}
