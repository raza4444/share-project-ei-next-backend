<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Monitors;


use App\Entities\Monitors\MonitorStates;
use App\Http\Controllers\AbstractInternController;
use App\Repositories\Monitors\MonitorFilter;
use App\Services\Core\PermissionItems;
use App\Services\Monitors\MonitorService;
use Illuminate\Http\Request;

class MonitorController extends AbstractInternController
{

    private $monitorService;

    public function __construct(MonitorService $monitorService)
    {
        $this->monitorService = $monitorService;
    }

    public function append(Request $request)
    {

        //Rechte sicherstellen
        if (!$this->hasPermission()) {
            return $this->accessDeniedWithReason('user-not-granted-system-monitor');
        }

        $realm = $request->get('realm');
        $referenceId = $request->get('referenceid');
        $details = $request->get('details');

        $monitor = $this->monitorService->ensureMonitor($realm, $referenceId);
        $monitor->withLastUpdateToNow();
        $monitor->withGlobalState(MonitorStates::SUCCESS);

        if (is_array($details)) {
            foreach ($details as $d) {
                $dstate = MonitorStates::fromString($d['state']);
                if ($dstate == MonitorStates::ERROR) $monitor->withGlobalState($dstate);
                $monitor->withNewDetail($dstate, $d['message']);
            }
        }

        $monitor->save();

        return $this->noContent();
    }

    public function find(Request $request)
    {

        //Rechte sicherstellen
        if (!$this->hasPermission()) {
            return $this->accessDeniedWithReason('user-not-granted-system-monitor');
        }

        $filter = new MonitorFilter();
        if ($request->has('realm')) $filter->realm = $request->get('realm');
        if ($request->has('top')) $filter->top = $request->get('top') * 1;
        return $this->json(200, $this->monitorService->findByFilter($filter));
    }

    public function findForDomainSetup()
    {
        //Rechte sicherstellen
        if (!$this->hasPermission()) {
            return $this->accessDeniedWithReason('user-not-granted-system-monitor');
        }

        return $this->json(200, $this->monitorService->findForDomainSetup());
    }

    private function hasPermission()
    {
        $fac = $this->getCurrentUser()->permissionFacade();
        return $fac->canAccessItem(PermissionItems::SYSTEM_MONITOR);
    }

}
