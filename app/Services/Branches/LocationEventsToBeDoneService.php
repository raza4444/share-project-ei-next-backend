<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use App\Utils\DBUtils;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class WorkEvent
 * @package App\Services\Branches
 * @property int id
 * @property int cid
 * @property int arbeitskategorie
 * @property int wiedervorlage
 * @property int lastuserid
 */
class WorkEvent extends \stdClass
{
}

class LocationEventsToBeDoneService
{

    public function countToDo()
    {

        $w = date('w') * 1;

        $fieldStart = 'wd' . $w . '_hour_start';
        $fieldEnd = 'wd' . $w . '_hour_end';
        $hour = date('H') * 1;

        $q = "
          select 
            count(*) as anzahl 
          from 
            campaign_location_events e
            inner join campaign_locations l on l.id = e.schoolId
            inner join campaign_location_categories c on l.locationCategoryId = c.id
            inner join campaign_location_event_matcher_rules r on r.categoryId = c.id
          where 
            (e.deleted_at is null)
            and (l.deleted_at is null)
            and (e.showAfter <= ?) 
            and (e.timestamp <= ?) 
            and (e.done is null or e.done=0) 
            and (e.lockedUserId is null)
            and (
              (
                (wiedervorlage = 0)
                and (r.$fieldStart is not null)
                and (r.$fieldEnd is not null)
                and (r.$fieldStart <= $hour)
                and (r.$fieldEnd >= $hour)                
              )
              or
              (
                (wiedervorlage = 1)
              )
            )
          ";

        $fines = self::finesV2();
        return DBUtils::quickCount($q, 'anzahl', [$fines, $fines]);
    }

    /**
     * @return WorkEvent[]
     */
    public function loadOpenEvents()
    {
        $w = date('w') * 1;
        $fieldStart = 'wd' . $w . '_hour_start';
        $fieldEnd = 'wd' . $w . '_hour_end';
        $hour = date('H') * 1;

        $q = "
          select 
            e.id,
            c.id as cid,
            e.arbeitskategorie,
            e.wiedervorlage,
            e.agentLastChangeId as lastuserid
          from 
            campaign_location_events e
            inner join campaign_locations l on l.id = e.schoolId
            inner join campaign_location_categories c on l.locationCategoryId = c.id
            inner join campaign_location_event_matcher_rules r on r.categoryId = c.id
          where 
            (e.deleted_at is null)
            and (l.deleted_at is null)
            and (e.showAfter <= ?) 
            and (e.timestamp <= ?) 
            and (e.done is null or e.done=0) 
            and (e.lockedUserId is null)
            and (
              (
                (wiedervorlage = 0)
                and (r.$fieldStart is not null)
                and (r.$fieldEnd is not null)
                and (r.$fieldStart <= $hour)
                and (r.$fieldEnd >= $hour)                
              )
              or
              (
                (wiedervorlage = 1)
              )
            )
            order by wiedervorlage desc, e.showAfter asc
          ";
        $fines = self::finesV2();

        return DB::select($q, [$fines, $fines]);
    }

    private static function finesV2()
    {
        return Carbon::now()->format('Y-m-d H:i:s');
    }


}
