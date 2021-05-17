<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use App\Entities\Repair\RepairAble;
use App\Entities\Repair\RepairOutput;
use Illuminate\Support\Facades\DB;

class LocationDuplicateDeletionService implements RepairAble
{

    private $locationDeletionService;

    public function __construct(
        LocationDeletionService $locationDeletionService
    )
    {
        $this->locationDeletionService = $locationDeletionService;
    }

    public function deleteDuplicatesByPhoneNumber()
    {
        $q = 'select count(*) as anzahl,
          phonenumber
          from campaign_locations
          where deleted_at is null and phonenumber is not null and length(phonenumber)>3
          group by phonenumber
          having anzahl > 1';

        $list = DB::select($q);
        foreach ($list as $loc) {
            $number = $loc->phonenumber;
            RepairOutput::line($number);

            $q = 'select id from campaign_locations where phoneNumber=?';
            $locations = DB::select($q,[$number]);
            foreach ($locations as $loc) {
                $lid = $loc->id;
                $this->locationDeletionService->deleteLocationAndEventsLogically($lid);
                RepairOutput::line($lid);
            }

        }

    }

    function repair()
    {
        $this->deleteDuplicatesByPhoneNumber();
    }


}
