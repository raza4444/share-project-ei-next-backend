<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Publics\Branches;

use App\Http\Controllers\Publics\AbstractPublicsController;
use App\Repositories\Branches\LocationEventDeactivationService;
use Illuminate\Http\Request;

/**
 * rest api for importaing companies into narev system
 *
 * Class PublicCompanyController
 */
class PublicEventDeactivationController extends AbstractPublicsController
{

    private $locationEventDeactivationService;

    public function __construct(
        LocationEventDeactivationService $locationEventDeactivationService
    )
    {
        $this->locationEventDeactivationService = $locationEventDeactivationService;
    }

    public function deactivateByPhoneNumbers(Request $request)
    {
        $cc = 0;

        $lines = explode("\n", str_replace("\r", "", $request->getContent()));
        foreach ($lines as $number) {
            $number = trim($number);
            if (strlen($number) < 4) continue;
            if ($number[0] != '+') $number = '+'.$number;
            $result = $this->locationEventDeactivationService->setEventsToNotInterestedByLocationPhoneNumber($number);
            if ($result) $cc++;
        }

        return $this->singleJson(['count' => $cc]);
    }

}
