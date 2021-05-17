<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Monitors;


class MonitorStates
{

    const UNTOUCHED = -1;

    const SUCCESS = 0;

    const ERROR = 9;

    /**
     * @param $s
     * @return int
     * @throws \Exception
     */
    public static function fromString($s)
    {
        $s .= '';
        $l = strtolower($s);
        if ($s == self::UNTOUCHED . '' || $l == 'untouched') {
            return self::UNTOUCHED;
        }
        if ($s == self::SUCCESS . '' || $l == 'success') {
            return self::SUCCESS;
        }
        if ($s == self::ERROR . '' || $l == 'error') {
            return self::ERROR;
        }
        throw new \Exception('Ungültiger Status <' . $s . '>');
    }

}
