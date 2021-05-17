<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Monitors;


use App\Entities\Monitors\Monitor;
use App\Entities\Monitors\MonitorsRealmsKnown;
use App\Utils\DateTimeUtils;
use Illuminate\Support\Facades\DB;

class MonitorRepository
{

    /**
     * @param $id
     * @return Monitor|null
     */
    public function byId($id)
    {
        return $this->query()->where('id', '=', $id)->first();
    }

    /**
     * @param $realm
     * @param $referenceId
     * @return Monitor|null
     */
    public function byCoreData($realm, $referenceId)
    {
        return $this->query()->where('realm', '=', $realm)->where('referenceid', '=', $referenceId)->first();
    }

    /**
     * @param $realm
     * @param $referenceId
     * @return Monitor
     */
    public function createQuick($realm, $referenceId)
    {
        $m = new Monitor();
        $m->realm = $realm;
        $m->referenceid = $referenceId;
        $m->created = DateTimeUtils::nowAsString();
        $m->save();
        return $m;
    }

    /**
     * @param MonitorFilter $filter
     * @return Monitor[]
     */
    public function findByFilter(MonitorFilter $filter)
    {
        $q = $this->query();

        if ($filter->realm !== null) {
            $q->where('realm', '=', $filter->realm);
        }

        if ($filter->top !== -1) {
            $q->limit($filter->top);
        }

        $q->with('details');
        return $q->get();

    }

    public function findForDomainSetup()
    {
        $q =
            "select 
            l.id as locationid,
            l.title,
            l.domain,
            m.id as monitorid,
            m.lastupdate,
            m.state
        from campaign_locations l
        left join " . $this->table() . " m on m.referenceid=l.id and m.realm='" . MonitorsRealmsKnown::CUSTOMER_SSL . "'
        where 
            (l.domain is not null) and (length(l.domain)>2) 
        ";

        return DB::select($q);
    }

    private function query()
    {
        return Monitor::query();
    }

    private function table()
    {
        return (new Monitor())->getTable();
    }

}
