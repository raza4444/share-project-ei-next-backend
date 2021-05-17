<?php
/**
 * by stephan scheide
 */

namespace App\Utils;


class Asserts
{

    public static function isTrue($t, $message)
    {
        if (!$t) {
            throw new \Exception($message);
        }
    }

    public static function notNull($n, $message)
    {
        self::isTrue($n !== null, $message);
    }

}