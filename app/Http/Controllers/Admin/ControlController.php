<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\AbstractInternController;
use Illuminate\Support\Facades\DB;

class ControlController extends AbstractInternController
{

    private static function formatErlaubnisAnrufen($v)
    {
        return $v === 0 ? 'keine Angabe' : ($v === 1 ? 'ja' : 'nein');
    }

    private static function formatResult($v)
    {
        if ($v == 'interest') {
            return 'Interesse';
        } else if ($v == 'showAgain') {
            return 'Wiedervorlage';
        } else if ($v == 'notUsable') {
            return 'nicht verwertbar';
        } else if ($v == 'noInterest') {
            return 'kein Interesse';
        } else {
            return 'k.A.';
        }
    }

    public function control()
    {

        $config = [
            'Erlaubnisse' => ['select count(*) as anzahl,erlaubnis_anrufen from campaign_location_events group by erlaubnis_anrufen', []],
            'Letzte Ereignisse' => ['select id as ereignisid,schoolid as unternehmensid,done as erledigt,updated_at,result as ergebnis,finishedTimestamp,erlaubnis_anrufen from campaign_location_events order by updated_at desc limit 50', []]
        ];

        $result = [];

        foreach ($config as $k => $v) {
            $result[$k] = DB::select($v[0], $v[1]);
        }

        foreach ($result as $name => $table) {
            foreach ($table as $row) {
                if ($name == 'Erlaubnisse' || $name == 'Letzte Ereignisse') {
                    $row->erlaubnis_anrufen = self::formatErlaubnisAnrufen($row->erlaubnis_anrufen);
                }
                if ($name == 'Letzte Ereignisse') {
                    $row->ergebnis = self::formatResult($row->ergebnis);
                }
            }
        }

        return $this->singleJson($result);
    }

}
