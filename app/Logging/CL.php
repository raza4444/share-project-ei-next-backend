<?php
/**
 * by stephan scheide
 */

namespace App\Logging;


class CL
{

    /**
     * @var AppLogger
     */
    private static $logger;

    public static function setCurrent(AppLogger $logger)
    {
        self::$logger = $logger;
    }

    public static function debug($params)
    {
        self::$logger->debug($params);
    }

}