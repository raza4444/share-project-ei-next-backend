<?php
/**
 * by stephan scheide
 */

namespace App\Logging;


class BlackHoleLogFacade implements LogFacade
{

    public static function createNew()
    {
        return new BlackHoleLogFacade();
    }

    public function info($message)
    {
    }

    public function error($message)
    {
    }

    public function exception(\Throwable $ex)
    {
    }


}
