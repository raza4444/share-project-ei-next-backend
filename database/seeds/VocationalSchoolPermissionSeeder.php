<?php

use Illuminate\Database\Seeder;
use App\Entities\Core\Permissions;
use App\Repositories\Core\VocationalSchoolRepository;

class VocationalSchoolPermissionSeeder extends Seeder
{

    private $vocationalSchoolRepository;

    public function __construct(
        VocationalSchoolRepository $vocationalSchoolRepository

    ) {
        $this->vocationalSchoolRepository = $vocationalSchoolRepository;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = $this->vocationalSchoolRepository->getVocationalSchoolSchedulePermissions();
        $this->createPermissions($permissions);
    }

    /**
     * @param array $permissionTypes
     * @return void
     */

    private function createPermissions($permissionTypes)
    {
        foreach ($permissionTypes as $permissionType) {
            $existingUrl =  Permissions::where('name', $permissionType)->first();
            if (is_null($existingUrl) || !isset($existingUrl)) {
                Permissions::create([
                    'name' => $permissionType,
                    'type' => 'vocational-school-schedule'
                ]);
            }
        }
    }
}
