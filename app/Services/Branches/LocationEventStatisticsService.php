<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use Illuminate\Support\Facades\DB;

class LocationEventStatisticsService
{

    public function eventsPerStateAndCat()
    {
        $q = "
        select
        c.title as kategorie,
        b.name as bundesland,
            count(*) as alle_ereignisse,
                    sum(if( (e.deleted_at is not null),1,0)) as geloescht,
                    sum(if( (e.done=0 and e.deleted_at is null),1,0)) as offen_oder_wiedervorlage,
                    sum(if( (e.done=0 and e.result is null and e.deleted_at is null),1,0)) as offen_noch_nicht_angefasst,
                    sum(if( (e.done=1 and e.deleted_at is null),1,0)) as done,
                    sum(if( (e.done=0 and e.result='showAgain' and e.deleted_at is null),1,0)) as in_wiedervorlage,
                    sum(if( (e.done=1 and e.result='noInterest' and e.deleted_at is null),1,0)) as kein_interesse
        
        from campaign_location_events e
        inner join campaign_locations l on e.schoolid = l.id
        inner join campaign_location_categories c on c.id = l.locationCategoryId
        inner join bundeslaender b on b.id = l.bundeslandid
        group by kategorie,bundesland order by kategorie,bundesland";

        return DB::select($q);
    }

}
