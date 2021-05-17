<?php
/**
 * Created by PhpStorm.
 * User: scheidest
 * Date: 27.08.2017
 * Time: 12:06
 */

namespace App\Utils;


use Carbon\Carbon;

class DateTimeUtils
{

    /**
     * Liefert das Pendant eines Wochentages im letzten Jahr
     * Dabei wird die Kalenderwoche hergenommen
     * @param Carbon $l
     * @return Carbon
     */
    public static function todayWeekdayLastYear(Carbon $l)
    {
        $week = $l->format('W');
        $year = $l->format('Y');
        $w = $l->format('w');
        $days = $w == 0 ? 6 : $w - 1;
        $tmp = new Carbon();
        $tmp->setISODate($year - 1, $week);
        $tmp->addDays($days);
        //echo $tmp->format('Y-m-d');
        return $tmp;
    }

    public static function isTimestamp($t)
    {
        return $t !== null && strlen($t) > 10;
    }

    public static function humanDate($ymd)
    {
        if (strlen($ymd) < 10) return '';
        $ymd = substr($ymd, 0, 10);
        $tmp = explode('-', $ymd);
        return $tmp[2] . '.' . $tmp[1] . '.' . $tmp[0];
    }

    public static function makeValue($y, $m, $d)
    {
        return $y * 10000 + $m * 100 + $d;
    }

    public static function makeValueNow()
    {
        return self::makeValue(date('Y'), date('m'), date('d'));
    }

    public static function nowAsString()
    {
        return date('Y-m-d H:i:s');
    }

    public static function timestampFromYMDHIS($str)
    {
        return strtotime($str);
    }

    public static function dateOnlyToYMD($str)
    {
        if (strlen($str) < 10) return null;
        if (strlen($str) > 10) $str = substr($str, 0, 10);
        if ($str[4] == '-') return $str;
        if ($str[2] == '.') {
            //12.05.2018
            //0123456789
            return substr($str, 6, 4) . '-' . substr($str, 3, 2) . '-' . substr($str, 0, 2);
        }
        return null;
    }

    public static function makeDataDateFromComponents($y, $m, $d)
    {
        return date('Y-m-d', mktime(0, 0, 0, $m, $d, $y));
    }


}