<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Shared\Misc\Utils\DBUtils;

/**
 * a service which checks the relationship between schools and their events
 * Class SchoolsAndEventsRelationCheckService
 * @package Modules\Companies\Services
 */
class SchoolsAndEventsRelationCheckService
{

    /**
     * returns the id of companies which have more than one call active call back event
     * @return int[]
     */
    public function findCompanyIdsWithDuplicateCallBackEvents()
    {
        $q = "
        select
            e.schoolid,
            count(*) as anzahl
        from campaign_location_events e
        where 
              (e.done is null or e.done=0)
              and (e.arbeitskategorie=1)
              and (e.deleted_at is null)
        group by e.schoolid
        having anzahl > 1 
        order by e.schoolid asc";

        $result = DB::select($q);
        return array_map(function ($resultItem) {
            return $resultItem->schoolid;
        }, $result);
    }

    /**
     * deletes the highest active call backend of given school
     * @param $schoolId
     * @return bool
     */
    public function deleteNewestCallBackEventOfCompany($schoolId)
    {
        $q = "
        select 
               id 
        from campaign_location_events e 
        where 
              schoolid=$schoolId
              and (e.done is null or e.done = 0)
              and (e.arbeitskategorie=1)
              and (e.deleted_at is null)
        order by id desc";

        $id = DBUtils::first($q);
        if ($id === null) {
            return false;
        }

        $id = $id->id;

        DB::table('campaign_location_events')
            ->where('id', '=', $id)
            ->update(['deleted_at' => Carbon::now()]);

        return true;

    }
}
