<?php
/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use Illuminate\Support\Facades\DB;

class LocationStatisticsService
{
    /**
     * returns statistics of amount of locations per bundesland and kategorie
     * flat array
     *
     * @return array
     */
    public function locationsPerCategoryAndState()
    {
        $q = "
        select
            c.title as kategorie,
            b.name as bundesland,
            count(*) as anzahl_unternehmen,
            sum(if( (l.deleted_at is not null),1,0)) as davon_geloescht
        from campaign_locations l
        inner join campaign_location_categories c on c.id = l.locationCategoryId
        inner join bundeslaender b on b.id = l.bundeslandid
        group by kategorie,bundesland order by kategorie,bundesland";

        return DB::select($q);
    }
}
