<?php
/**
 * by stephan scheide
 */

namespace App\Utils;


class QuickTemplateEngine
{

    private $values;

    public function __construct()
    {
        $this->values = [];
    }

    public static function create()
    {
        return new QuickTemplateEngine();
    }

    public function withValue($key, $value)
    {
        $this->values[$key] = $value;
        return $this;
    }

    public function withValues($arr)
    {
        foreach ($arr as $k => $v) {
            $this->values[$k] = $v;
        }
        return $this;
    }

    public function applyToString($str)
    {
        $old = $str;
        do {
            $old = $str;
            $str = $this->applyOnce($str);
        } while ($old != $str);
        return $str;
    }

    private function applyOnce($str)
    {
        foreach ($this->values as $k => $v) {
            $str = str_replace('%' . $k . '%', $v, $str);
        }
        return $str;
    }


}
