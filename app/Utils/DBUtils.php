<?php
/**
 * by stephan scheide
 */

namespace App\Utils;


use Illuminate\Support\Facades\DB;

class DBUtils
{

    public static function quickCount($q, $colNameForCount = 'anzahl', $params = [])
    {
        return DB::select($q, $params)[0]->$colNameForCount;
    }

    public static function scalar($q, $name, $params = [])
    {
        $items = DB::select($q, $params);
        return count($items) == 0 ? null : $items[0]->$name;
    }

    public static function first($q, $params = [])
    {
        $items = DB::select($q, $params);
        return count($items) == 0 ? null : $items[0];
    }

}
