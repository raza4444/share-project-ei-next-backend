<?php
/**
 * by stephan scheide
 */

namespace App\Logging;


class AppLogger
{

    private $prefix = null;

    public function __construct($name, $uniqueId = null)
    {
        $this->prefix = $name;
        if ($uniqueId != null) $this->prefix .= '-' . $uniqueId;
    }

    public function debug($params = '')
    {
        QuickLog::quickWithName($this->prefix, $params);
    }

}
