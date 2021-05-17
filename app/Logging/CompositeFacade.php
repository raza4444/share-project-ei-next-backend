<?php
/**
 * by stephan scheide
 */

namespace App\Logging;


class CompositeFacade implements LogFacade
{

    private $children;

    public function __construct()
    {
        $this->children = [];
    }

    public static function create()
    {
        return new CompositeFacade();
    }

    public function withMany(...$facades)
    {
        foreach ($facades as $f) {
            $this->children[] = $f;
        }
        return $this;
    }

    public function withAtEnd(LogFacade $f)
    {
        $this->children[] = $f;
        return $this;
    }

    public function withAt(LogFacade $f, $index)
    {
        $this->children[$index] = $f;
        return $this;
    }

    public function withBlackHoleAt($index)
    {
        return $this->withAt(BlackHoleLogFacade::createNew(), $index);
    }

    public function info($message)
    {
        foreach ($this->children as $c) {
            $c->info($message);
        }
    }

    public function error($message)
    {
        foreach ($this->children as $c) {
            $c->error($message);
        }
    }

    public function exception(\Throwable $ex)
    {
        foreach ($this->children as $c) {
            $c->exception($ex);
        }
    }


}
