<?php

use Illuminate\Database\Seeder;
use App\Entities\Core\Permissions;
use App\Repositories\Branches\LocationRepository;
use App\Repositories\Branches\LocationNoteRepository;
use App\Repositories\Duties\DutyRowRepository;
use App\Repositories\Branches\AppointmentRepository;

class CustomerOverviewSeeder extends Seeder
{


    private $locationRepository;
    private $locationNoteRepository;
    private $dutyRowRepository;
    private $appointmentRepository;

    public function __construct(
        LocationRepository $locationRepository,
        LocationNoteRepository $locationNoteRepository,
        DutyRowRepository $dutyRowRepository,
        AppointmentRepository $appointmentRepository
    ) {
        $this->locationRepository = $locationRepository;
        $this->locationNoteRepository = $locationNoteRepository;
        $this->dutyRowRepository = $dutyRowRepository;
        $this->appointmentRepository = $appointmentRepository;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionOFRegistrationOptionsBlock = $this->locationRepository->getPermissionOfRegistrationOptionsBlock();
        $this->createPermissions($permissionOFRegistrationOptionsBlock);

        $permissionOfWebsiteDataBlock = $this->locationRepository->getPermissionOfWebsiteDataBlock();
        $this->createPermissions($permissionOfWebsiteDataBlock);

        $permissionOfRevocationORCancellationBlock = $this->locationRepository->getPermissionOfRevocationORCancellationBlock();
        $this->createPermissions($permissionOfRevocationORCancellationBlock);

        $permissionOfCustomerDataBlock = $this->locationRepository->getPermissionOfCustomerDataBlock();
        $this->createPermissions($permissionOfCustomerDataBlock);

        $permissionOfWerbreaktionBlock = $this->locationRepository->getPermissionOfWerbreaktionBlock();
        $this->createPermissions($permissionOfWerbreaktionBlock);

        $permissionOfVerkaufsdatenBlock = $this->locationRepository->getPermissionOfVerkaufsdatenBlock();
        $this->createPermissions($permissionOfVerkaufsdatenBlock);

        $permissionOfNotesOfCompanyBlock = $this->locationNoteRepository->getPermissionOfNotesOfCompanyBlock();
        $this->createPermissions($permissionOfNotesOfCompanyBlock);

        $permissionOfTasksBlock = $this->dutyRowRepository->getPermissionOfTasksBlock();
        $this->createPermissions($permissionOfTasksBlock);

        $permissionOfAppointmentInCompanyDetailBlock = $this->appointmentRepository->permissionOfAppointmentInCompanyDetailBlock();
        $this->createPermissions($permissionOfAppointmentInCompanyDetailBlock);
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
                ]);
            }
        }
    }
}
