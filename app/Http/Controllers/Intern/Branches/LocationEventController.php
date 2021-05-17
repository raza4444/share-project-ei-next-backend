<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Branches;


use App\Entities\Branches\LocationEvent;
use App\Http\Controllers\AbstractInternController;
use App\Repositories\Branches\BasicEventFilter;
use App\Repositories\Branches\LocationEventRepository;
use App\Repositories\Branches\LocationRepository;
use App\Services\Branches\LocationEventCreationService;
use App\Services\Branches\LocationEventStatisticsService;
use Illuminate\Http\Request;

class LocationEventController extends AbstractInternController
{

    private $locationRepository;

    private $locationEventCreationService;

    private $locationEventRepository;

    private $locationEventStatisticsService;

    public function __construct(
        LocationRepository $locationRepository,
        LocationEventStatisticsService $locationEventStatisticsService,
        LocationEventRepository $locationEventRepository,
        LocationEventCreationService $locationEventCreationService
    )
    {
        $this->locationRepository = $locationRepository;
        $this->locationEventCreationService = $locationEventCreationService;
        $this->locationEventRepository = $locationEventRepository;
        $this->locationEventStatisticsService = $locationEventStatisticsService;
    }

    public function createFromLocation(Request $request)
    {
        $id = $request->get('locationId');
        $location = $this->locationRepository->byIdActive($id);
        if ($location === null) return $this->notFound();
        $event = $this->locationEventCreationService->createForLocation($location);
        return $this->json(200, $event);
    }

    public function byId($id)
    {
        /**
         * @var $event LocationEvent
         */
        $event = $this->locationEventRepository->byIdActive($id);
        if ($event === null) return $this->notFound();
        $event->location;
        if ($event->location) $event->location->locationCategory;
        return $this->singleJson($event, 200);
    }

    public function findGeneric(Request $request)
    {
        $view = $request->has('view') ? $request->get('view') : null;
        $filter = new BasicEventFilter();
        $filter->view = $view;

        $list = $this->locationEventRepository->byBasicFilter($filter);
        return $this->singleJson($list);
    }

    public function eventsPerStateAndCat()
    {
        return $this->json(200, $this->locationEventStatisticsService->eventsPerStateAndCat());
    }

}
