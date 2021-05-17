<?php
/**
 * by stephan scheide
 */

namespace App\Console\Commands;


use App\Repositories\Admin\InternUserRepository;
use App\Services\Branches\AppointmentService;
use App\Utils\Asserts;
use Illuminate\Console\Command;

class DeleteAppointmentsOfAdmin extends Command
{

    protected $name = 'application:delete-appointments-of-admin';

    public function handle(
        InternUserRepository $internUserRepository,
        AppointmentService $service
    )
    {
        $admin = $internUserRepository->findMainAdmin();
        Asserts::notNull($admin, 'Admin nicht gefunden');

        $service->purgeAllCreatedByUser($admin->id);
    }


}