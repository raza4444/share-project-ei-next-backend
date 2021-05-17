<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Tasks;


use App\Entities\Core\AbstractModel;

/**
 * Class GenericTask
 * @package App\Entities\Tasks
 *
 * @property int done
 * @property string finishedAt
 * @property int finishedBy
 * @property int parentId
 * @property int hasSubTasks
 * @property int type
 * @property string title
 * @property string businessType
 *
 */
class GenericTask extends AbstractModel
{

    const TYPE_MARK_AS_DONE = 0;

    const TYPE_CONTAINER = 1;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->done = 0;
    }

    public static function createSingleCheckActionTask($title, $businessType)
    {
        $g = new GenericTask();
        $g->title = $title;
        $g->hasSubTasks = 0;
        $g->type = self::TYPE_MARK_AS_DONE;
        $g->businessType = $businessType;
        $g->save();
        return $g;
    }

    /**
     * @param $title
     * @return GenericTask
     */
    public static function createSimpleContainer($title, $businessType)
    {
        $g = new GenericTask();
        $g->title = $title;
        $g->hasSubTasks = 1;
        $g->type = self::TYPE_CONTAINER;
        $g->businessType = $businessType;
        $g->save();
        return $g;
    }

    /**
     * @param GenericTask $container
     * @param $title
     * @return GenericTask
     */
    public static function createLeaf(GenericTask $container, $title, $businessType)
    {
        $g = new GenericTask();
        $g->title = $title;
        $g->hasSubTasks = 0;
        $g->type = self::TYPE_MARK_AS_DONE;
        $g->parentId = $container->id;
        $g->businessType = $businessType;
        $g->save();
        return $g;
    }

    public function addLeaf($title, $businessType)
    {
        return self::createLeaf($this, $title, $businessType);
    }

    /**
     * @return CounterTaskEvent|null
     */
    public function getCounterTaskEvent()
    {
        return CounterTaskEvent::query()
            ->where('mainTaskId', '=', $this->id)
            ->first();
    }

}