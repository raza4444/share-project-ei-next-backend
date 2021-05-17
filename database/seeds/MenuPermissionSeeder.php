<?php

use Illuminate\Database\Seeder;
use App\Entities\Menus\Menu;
use App\Entities\Menus\MenuItem;
use App\Entities\Core\Permissions;

class MenuPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = Menu::distinct('titel')->pluck('titel')->all();
        $menusPermission = array_map(function($menu) { return str_replace(' ','-', strtolower($menu)); }, $menus);
        $this->createPermissions($menusPermission, 'menu');
        $menuItems = MenuItem::distinct('titel')->pluck('titel')->all();
        $menuItemPermission = array_map(function($menu) { return str_replace(' ','-', strtolower($menu)); }, $menuItems);
        $this->createPermissions($menuItemPermission, 'menu-item');
    }

    /**
     * @param array $permissionTypes
     * @return void
     */

    private function createPermissions($permissionTypes, $type) {
        foreach ($permissionTypes as $permissionType) {
            $existingPermission =  Permissions::where('name', $permissionType)->first();
            if (is_null($existingPermission) || !isset($existingPermission)) {
                Permissions::create([
                    'name' => 'menu-'.$permissionType,
                    'type'=> $type
                ]);
            }
        }
    }
}
