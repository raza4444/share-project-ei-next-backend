<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Branches;


class LocationEventDeactivationService
{

    private $locationRepository;

    private $locationEventRepository;

    public function __construct(
        LocationRepository $locationRepository,
        LocationEventRepository $locationEventRepository
    )
    {
        $this->locationRepository = $locationRepository;
        $this->locationEventRepository = $locationEventRepository;
    }

    public function setEventsToNotInterestedByLocationPhoneNumber($number)
    {
        $locations = $this->locationRepository->findLocationsByPhoneNumber($number);
        foreach ($locations as $loc) {
            $this->locationEventRepository->setResultToNotInterestedByLocationId($loc->id);
            return true;
        }
        return false;
    }

}