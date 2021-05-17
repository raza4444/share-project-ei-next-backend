<?php
/**
 * by stephan scheide
 */

namespace App\Utils;


class CheckBuilder
{

    const TYPE_INFO = 0;
    const TYPE_SUCCESS = 1;
    const TYPE_WARN = 2;
    const TYPE_ERROR = 3;

    private $data = [];

    private $currentField = 'instance';

    public static function create()
    {
        return new CheckBuilder();
    }

    public function field($name)
    {
        $this->currentField = $name;
        return $this;
    }

    public function message($type, $content, $condition = true)
    {
        if ($condition) {
            if (!array_key_exists($this->currentField, $this->data)) {
                $this->data[$this->currentField] = [];
            }
            $this->data[$this->currentField][] = ['type' => $type, 'content' => $content];
        }
        return $this;
    }

    public function error($content, $condition = true)
    {
        return $this->message(self::TYPE_ERROR, $content, $condition);
    }

    public function warn($content, $condition = true)
    {
        return $this->message(self::TYPE_WARN, $content, $condition);
    }

    public function toArray()
    {
        return $this->data;
    }

}
