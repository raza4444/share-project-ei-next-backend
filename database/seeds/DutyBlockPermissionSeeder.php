<?php

use Illuminate\Database\Seeder;
use App\Services\Duties\DutyBlockService;
use App\Services\Core\PermissionService;
use App\Entities\Core\Permissions;

class DutyBlockPermissionSeeder extends Seeder
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

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permissions::where('type', 'duty-configurator-block')->delete();
        $dutyBlocks = $this->dutyBlockService->getAllWithRowCount();
        $items = [];

        for ($i = 0; $i < sizeof($dutyBlocks); $i++) {

            $dutyBlockName = strtolower($dutyBlocks[$i]->name);
            $newDutyBlockName =  str_replace(' ', '-', strtolower(
                $dutyBlockName
            ));
            $this->permissionService->createPermission($newDutyBlockName, 'duty-configurator-block', 'aufgabenbloecke-');
        }
        //
    }
}
