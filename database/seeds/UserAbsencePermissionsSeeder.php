<?php

use Illuminate\Database\Seeder;
use App\Repositories\Core\PermissionRepository;
use App\Entities\Core\Permissions;

class UserAbsencePermissionsSeeder extends Seeder
{


    private $permissionRepository;

    public function __construct(
        PermissionRepository $permissionRepository
    ) {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permissionTypes = $this->permissionRepository->getUserAbsencePermissions();

        foreach ($permissionTypes as $permissionType) {
            $existingUrl =  Permissions::where('name', $permissionType)->first();
            if (is_null($existingUrl) || !isset($existingUrl)) {
                Permissions::create([
                    'name' => $permissionType,
                ]);
            }
        }
        //
    }
}
