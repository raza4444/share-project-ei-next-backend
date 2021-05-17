<?php
/**
 * by stephan scheide
 */

namespace App\Services\Monitors;


use App\Entities\Monitors\Monitor;
use App\Repositories\Monitors\MonitorFilter;
use App\Repositories\Monitors\MonitorRepository;

class MonitorService
{

    private $monitorRepository;

    public function __construct(MonitorRepository $monitorRepository)
    {
        $this->monitorRepository = $monitorRepository;
    }

    /**
     * @param $realm
     * @param $referenceId
     * @return Monitor
     */
    public function ensureMonitor($realm, $referenceId)
    {
        $m = $this->monitorRepository->byCoreData($realm, $referenceId);
        if ($m == null) {
            $m = $this->monitorRepository->createQuick($realm, $referenceId);
        }
        return $m;
    }

    public function findByFilter(MonitorFilter $filter)
    {
        return $this->monitorRepository->findByFilter($filter);
    }

    public function findForDomainSetup()
    {
        return $this->monitorRepository->findForDomainSetup();
    }

}
