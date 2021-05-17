<?php

/**
 * Created by PhpStorm.
 * User: kingster
 * Date: 16.12.2018
 * Time: 16:57
 */

namespace App\Http\Controllers\Admin;


use App\Entities\Menus\Menu;
use App\Entities\Menus\MenuItem;
use App\Http\Controllers\AbstractInternController;
use App\Repositories\Admin\MenuItemRepository;
use App\Repositories\Duties\DutyBlockRepository;
use App\Services\Core\PermissionService;
use Illuminate\Http\Request;

class MenuAdminController extends AbstractInternController
{
  private $menuItemRepository;
  private $dutyBlockRepository;
  private $permissionService;

  public function __construct(
    MenuItemRepository $menuItemRepository,
    DutyBlockRepository $dutyBlockRepository,
    PermissionService $permissionService
  ) {
    $this->menuItemRepository = $menuItemRepository;
    $this->dutyBlockRepository = $dutyBlockRepository;
    $this->permissionService = $permissionService;
  }

  public function menu()
  {
    $menus = Menu::all();
    return $this->singleJson(['menus' => $menus]);
  }

  public function menuItems()
  {
    $items = MenuItem::all()->toArray();
    $items = array_merge($items, $this->receiveTasksMenuItems());
    return $this->singleJson(['items' => $items]);
  }

  private function receiveTasksMenuItems()
  {
    $dutyBlocks = $this->dutyBlockRepository->getAll();
    $items = [];

    for ($i = 0; $i < sizeof($dutyBlocks); $i++) {

      $items[] = [
        'id' => $dutyBlocks[$i]->id,
        'schluessel' => 'a' . ($i + 1),
        'nummer' => $i + 1,
        'routerlink' => 'duty-list/blocks/' . $dutyBlocks[$i]->id,
        'titel' => $dutyBlocks[$i]->name,
        'locked' => 1,
        'menuid' => Menu::where('schluessel', 'a')->first()['id']
      ];
    }

    return $items;
  }

  public function deleteMenu($id)
  {
    $menu = Menu::find($id);
    $this->permissionService->deletePermission($menu->titel , 'menu', 'menu-');
    return $this->entityDelete($menu);
  }

  public function create(Request $request)
  {
    if (is_null($request->get('titel'))) {
      return $this->badRequestWithReason('Titel ist erforderlich');
    }
   $permission_id = $this->permissionService->createPermission($request->get('titel'), 'menu', 'menu-');
   $request->merge([
    'permission_id' => $permission_id,
  ]);
   $menu = $this->jsonAsEntity($request, Menu::class);
    $menu->save();
    return $this->singleJson($menu);
  }

  public function updateMenu($id, Request $request)
  {

    $menu = Menu::find($id);
    if ($request->get('titel') != $menu->titel) {
      $this->permissionService->updatePermission($menu->titel,  'menu', $request->get('titel'), 'menu-');
    }
    return $this->entityUpdate($request, $menu);
  }

  public function updateMenuItem(Request $request, $menuId)
  {
    $item = $request->json()->all();
    $savedItem = MenuItem::find($menuId);

    if ($savedItem) {
      $savedItem->menuId = $item['menuid'] * 1;
      $savedItem->titel = $item['titel'];
      $savedItem->routerlink = $item['routerlink'];
      $savedItem->nummer = $item['nummer'];
      $savedItem->schluessel = $item['schluessel'];
      // $savedItem->sichtbar = $item['sichtbar'] * 1;
      $savedItem->permission_id = $item['permission_id'];
      $savedItem->save();
    }

    return MenuItem::find($menuId);
  }

  public function updateManyMenuItems(Request $request)
  {
    $items = $request->json()->all();

    foreach ($items as $item) {
      $id = $item['id'];
      /**
       * @var MenuItem $loadedItem
       */
      

      $loadedItem = $this->menuItemRepository->byIdActive($id);

      if ($item['titel'] != $loadedItem->titel) {
        $this->permissionService->updatePermission($loadedItem->titel,  'menu-item', $item['titel'], 'menu-');
      }

      if ($loadedItem != null) {
        $loadedItem->menuId = $item['menuid'] * 1;
        $loadedItem->titel = $item['titel'];
        $loadedItem->routerlink = $item['routerlink'];
        $loadedItem->nummer = $item['nummer'];
        $loadedItem->schluessel = $item['schluessel'];
        // $loadedItem->sichtbar = $item['sichtbar'] * 1;
        $loadedItem->save();
      }
    }

    return $this->singleJson(MenuItem::all());
  }

  public function createMenuItem(Request $request)
  {
    if (is_null($request->get('titel'))) {
      return $this->badRequestWithReason('Titel ist erforderlich');
    }
    
    $permission_id = $this->permissionService->createPermission($request->get('titel'), 'menu-item', 'menu-');
    $item = $this->jsonAsEntity($request, MenuItem::class);
    $item->menuId = $request->get('menuid');
    $item->permission_id = $permission_id;
    $item->save();
    return $this->singleJson($item);
  }

  public function deleteMenuItem($id)
  {
    $menu = MenuItem::find($id);
    $this->permissionService->deletePermission($menu->titel , 'menu-item', 'menu-');
    $this->menuItemRepository->deleteById($id);
  }

  public function getMenuItemById($id)
  {
    $item = $this->menuItemRepository->byIdActive($id);
    if ($item == null) return $this->notFound();
    return $this->singleJson($item);
  }
}
