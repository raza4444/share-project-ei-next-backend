<?php

use Illuminate\Database\Seeder;
use App\Repositories\Duties\DutyBlockRepository;
use App\Repositories\Duties\DutyRowTemplRepository;
use App\Repositories\Duties\DutyTaskRepository;
use App\Repositories\Duties\DutyFollowUpRepository;
use App\Entities\Core\Permissions;

class DutyConfiguratorPermissionsSeeder extends Seeder
{


    private $dutyBlockRepository;
    private $dutyRowTemplRepository;
    private $dutyTaskRepository;
    private $dutyFollowUpRepository;

    public function __construct(
        DutyBlockRepository $dutyBlockRepository,
        DutyRowTemplRepository $dutyRowTemplRepository,
        DutyTaskRepository $dutyTaskRepository,
        DutyFollowUpRepository $dutyFollowUpRepository
    ) {
        $this->dutyBlockRepository = $dutyBlockRepository;
        $this->dutyRowTemplRepository = $dutyRowTemplRepository;
        $this->dutyTaskRepository = $dutyTaskRepository;
        $this->dutyFollowUpRepository = $dutyFollowUpRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $taskBlocksPermissionTypes = $this->dutyBlockRepository->getDutyConfiguratorTaskBlocksPermissions();
        $this->createPermissions($taskBlocksPermissionTypes);

        $taskLinesPermissionTypes = $this->dutyRowTemplRepository->getDutyConfiguratorTaskRowsPermissions();
        $this->createPermissions($taskLinesPermissionTypes);

        $tasksPermissionTypes = $this->dutyTaskRepository->getDutyConfiguratorTasksPermissions();
        $this->createPermissions($tasksPermissionTypes);

        $followUpPermissionTypes = $this->dutyFollowUpRepository->getDutyConfiguratorFollowUpPermissions();
        $this->createPermissions($followUpPermissionTypes);
        //
    }
    
    /**
     * @param array $permissionTypes
     * @return void
     */

    private function createPermissions($permissionTypes) {
        foreach ($permissionTypes as $permissionType) {
            $existingUrl =  Permissions::where('name', $permissionType)->first();
            if (is_null($existingUrl) || !isset($existingUrl)) {
                Permissions::create([
                    'name' => $permissionType,
                ]);
            }
        }
    }
}
