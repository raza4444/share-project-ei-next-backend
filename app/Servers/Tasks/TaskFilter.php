<?php
/**
 * by stephan scheide
 */

namespace App\Servers\Tasks;


class TaskFilter
{

    public $counterTaskId = 0;

    public $userId = 0;

    /**
     * default filter with counter and user
     *
     * @param $counterTaskId
     * @param $userId
     * @return TaskFilter
     */
    public static function newDefault($counterTaskId, $userId)
    {
        $t = new TaskFilter();
        $t->counterTaskId = $counterTaskId;
        $t->userId = $userId;
        return $t;
    }

}