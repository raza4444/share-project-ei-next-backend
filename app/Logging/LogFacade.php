<?php
/**
 * by stephan scheide
 */

namespace App\Logging;


interface LogFacade
{

    public function info($message);

    public function error($message);

    public function exception(\Throwable $ex);

}
