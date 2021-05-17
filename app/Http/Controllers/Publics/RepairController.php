<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Publics;


use App\Services\Branches\AppointmentRepairService;
use App\Services\Repair\RepairService;

class RepairController extends AbstractPublicsController
{

    private $repairService;

    private $locationEventAppointmentRepairService;

    public function __construct(
        RepairService $repairService,
        AppointmentRepairService $locationEventAppointmentRepairService
    )
    {
        $this->repairService = $repairService;
        $this->locationEventAppointmentRepairService = $locationEventAppointmentRepairService;
    }

    public function repair()
    {
        $this->locationEventAppointmentRepairService->repair();
        $this->repairService->repair();
    }

}
