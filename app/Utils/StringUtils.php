<?php
/**
 * by stephan scheide
 */

namespace App\Utils;

class StringUtils
{

    /**
     * erzeugt eine GUID
     * @return string
     */
    public static function createGUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    /**
     * prueft, ob der string leer ist
     * @param $str
     * @return bool
     */
    public static function isEmpty($str)
    {
        if ($str === null) return true;
        $s = trim($str);
        return strlen($s) === 0;
    }

    /**
     * nimmt aus einer menge strings den ersten nicht leeren
     * @param mixed ...$strings
     * @return mixed
     */
    public static function useNonEmpty(...$strings)
    {
        foreach ($strings as $str) {
            if (!self::isEmpty($str)) return $str;
        }
        return $strings[0];
    }

    public static function isTooShort($str, $atLeastLength)
    {
        if ($str === null) return true;
        $s = trim($str);
        return strlen($s) < $atLeastLength;
    }

    public static function ensureInteger($str, $fallBack = 0)
    {
        if (!preg_match('/^[1-9][0-9]*$/', $str)) {
            return $fallBack;
        }
        return $str * 1;
    }

    public static function toInt($str, $fallBack = null)
    {
        if ($str == null) return $fallBack;
        $i = preg_replace('/\D/', '', $str);
        return strlen($i) > 0 ? $i * 1 : $fallBack;
    }

    public static function firstUpper($str)
    {
        return $str == null || strlen($str) == 0 ? $str : strtoupper(substr($str, 0, 1)) . substr($str, 1);
    }

    public static function arrayToString($arr)
    {
        $str = '';
        foreach ($arr as $k => $v) {
            $str .= "$k=$v;";
        }
        return $str;
    }

    public static function onlyFiguresAndNumbers($str)
    {
        if ($str === null) {
            return null;
        }
        return preg_replace("/[^a-zA-Z0-9]/", '', $str);
    }
}
