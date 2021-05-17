<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Statistics;


use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultsByUserController extends AbstractInternController
{

    public function all(Request $request)
    {
        $from = null;
        $to = null;

        if ($request->has('from')) {
            $from = $request->get('from');
        }

        if ($request->has('to')) {
            $to = $request->get('to');
        }

        $qAppendix = '';
        $qParams = [];
        if ($from != null) {
            $qAppendix .= 'and (t.created_at >= ?)';
            $qParams[] = $from;
        }
        if ($to != null) {
            $qAppendix .= 'and (t.created_at <= ?)';
            $qParams[] = $to;
        }

        $q = "
        select
            count(*) as anzahl,
            result,
            u.username
        from location_event_tracks t
        inner join users u on u.id = t.userId
        where 
              (t.action='result') $qAppendix
        group by result,u.username
        order by u.username asc";

        $list = DB::select($q, $qParams);

        $map = [];
        foreach ($list as $entry) {
            $u = $entry->username;
            $r = $entry->result;
            if (!array_key_exists($u, $map)) {
                $map[$u] = [];
            }
            if (!array_key_exists($r, $map[$u])) {
                $map[$u][$r] = $entry->anzahl;
            }
        }

        //Map flachklopfen
        $items = [];
        foreach ($map as $username => $userData) {
            $row = ['username' => $username];
            foreach ($userData as $r => $rc) $row[$r] = $rc;
            $items[] = $row;
        }

        return $this->singleJson($items);
    }

}
