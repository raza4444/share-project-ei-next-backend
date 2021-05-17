<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;

use Modules\Check\Services\ICheckService;
use Modules\Check\ValueObjects\CheckResult;
use Modules\Check\ValueObjects\RepairResult;

/**
 * this service checks if there are any double events
 * (which leads same companies will be called twice - open events only)
 * Class DoubleEventsCheckService
 * @package Modules\Companies\Services
 */
class DoubleEventsCheckService implements ICheckService
{

    const ORIGIN = 'double-events-check-service';

    private $schoolsAndEventsRelationCheckService;

    public function __construct(
        SchoolsAndEventsRelationCheckService $schoolsAndEventsRelationCheckService
    )
    {
        $this->schoolsAndEventsRelationCheckService = $schoolsAndEventsRelationCheckService;
    }

    /**
     * checks if there are duplicates
     * @return CheckResult[]
     */
    public function performCheck()
    {
        $ids = $this->schoolsAndEventsRelationCheckService->findCompanyIdsWithDuplicateCallBackEvents();
        $result = [];
        foreach ($ids as $id) {
            $result[] = CheckResult::byError(self::ORIGIN, "Unternehmen $id hat zwei aktive Rueckrufeereignisse");
        }

        if (count($result) == 0) {
            $result[] = CheckResult::bySuccess(self::ORIGIN, "Kein Unternehmen mit doppelten Rueckrufereignissen");
        }

        return $result;
    }

    /**
     * removes double events
     * if there are more, the newest will be removed
     * @return array|RepairResult[]
     */
    public function tryRepair()
    {
        $companyIds = $this->schoolsAndEventsRelationCheckService->findCompanyIdsWithDuplicateCallBackEvents();
        $result = [];
        foreach ($companyIds as $id) {
            $serviceResult = $this->schoolsAndEventsRelationCheckService->deleteNewestCallBackEventOfCompany($id);
            if ($serviceResult) {
                $result[] = RepairResult::success(self::ORIGIN, "Ereignis geloescht");
            } else {
                $result[] = RepairResult::notRepairAble(self::ORIGIN, "Kein Ereignis zu Unternehmen mit $id gefunden");
            }
        }
        return $result;
    }


}
