<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use App\Repositories\Branches\LocationEventRepository;
use App\Repositories\Branches\LocationRepository;
use App\Servers\EventServerClient;
use App\Utils\PhoneNumberHelper\PhoneNumberHelperImpl;
use App\Utils\StringUtils;

class LocationDeactivationService
{

    private $locationRepository;

    private $locationEventRepository;

    private $phoneNumberHelper;

    public function __construct(
        LocationEventRepository $locationEventRepository,
        PhoneNumberHelperImpl $phoneNumberHelper,
        LocationRepository $locationRepository
    )
    {
        $this->phoneNumberHelper = $phoneNumberHelper;
        $this->locationRepository = $locationRepository;
        $this->locationEventRepository = $locationEventRepository;
    }

    public function deactivateLocationById($id)
    {
        $loc = $this->locationRepository->byId($id);
        if ($loc === null) return;

        $loc->delete();

        $this->locationEventRepository->deactivateByLocationId($id);
    }

    public function deactivateByPhoneNumber($number)
    {
        if (StringUtils::isTooShort($number, 3)) {
            return;
        }

        $phoneNumber = $this->phoneNumberHelper->correctPhoneNumber($number);

        $locations = $this->locationRepository->findLocationsByPhoneNumber($phoneNumber);

        foreach ($locations as $loc) {
            $this->deactivateLocationById($loc->id);
        }

        $client = new EventServerClient();
        $client->forceReloadOfEvents();
    }

}