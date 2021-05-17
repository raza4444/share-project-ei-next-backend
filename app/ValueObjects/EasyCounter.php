<?php
/**
 * by stephan scheide
 */

namespace App\ValueObjects;


class EasyCounter
{

    private $counts = [];

    public function __construct()
    {
    }

    public function inc($name, $value = 1)
    {
        if (!array_key_exists($name, $this->counts)) {
            $this->counts[$name] = 0;
        }
        $this->counts[$name] += $value;
    }

    public function __toString()
    {
        return json_encode($this->counts);
    }

}
