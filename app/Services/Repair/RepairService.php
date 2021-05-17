<?php
/**
 * by stephan scheide
 */

namespace App\Services\Repair;


use App\Entities\Repair\RepairAble;
use App\Services\Branches\LocationDuplicateDeletionService;
use App\Services\Customers\CustomerPasswordService;

class RepairService implements RepairAble
{

    /**
     * @var RepairAble[]
     */
    private $services = [];

    public function __construct(
        LocationDuplicateDeletionService $locationDuplicateDeletionService,
        CustomerPasswordService $customerPasswordService
    )
    {
        $this->services[] = $locationDuplicateDeletionService;
        $this->services[] = $customerPasswordService;
    }

    public function repair()
    {
        foreach ($this->services as $service) {
            $service->repair();
        }
    }


}
