<?php

use Illuminate\Database\Seeder;
use App\Entities\Menus\Menu;
use App\Entities\Menus\MenuItem;
use App\Services\Core\PermissionService;

class MenuPermissionLinkSeeder extends Seeder
{
    private $permissionService;

    public function __construct(
        PermissionService $permissionService
    ) {
        $this->permissionService = $permissionService;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->menuLinksPermission();
        $this->menuItemsLinksPermission();
        //
    }

    private function menuLinksPermission()
    {
        $menus = Menu::all();
        foreach ($menus as $menu) {
            $menuName = strtolower($menu->titel);
            $permission =  str_replace(' ', '-', strtolower($menuName));
            $permission =  'menu-' . $permission;
            $getSinglePermission = $this->permissionService->findByName($permission, 'menu');
            if ($getSinglePermission) {
                $menuObj = Menu::find($menu->id);
                if (is_null($menuObj->permission_id)) {
                    $menuObj->permission_id = $getSinglePermission->id;
                    $menuObj->update();
                }
            }
        }
    }

    private function menuItemsLinksPermission()
    {
        $menus = MenuItem::all();
        foreach ($menus as $menu) {
            $menuName = strtolower($menu->titel);
            $permission =  str_replace(' ', '-', strtolower($menuName));
            $permission =  'menu-' . $permission;
            $getSinglePermission = $this->permissionService->findByName($permission, 'menu-item');
            if ($getSinglePermission) {
                $menuObj = MenuItem::find($menu->id);
                if (is_null($menuObj->permission_id)) {
                    $menuObj->permission_id = $getSinglePermission->id;
                    $menuObj->update();
                }
            }
        }
    }
}
