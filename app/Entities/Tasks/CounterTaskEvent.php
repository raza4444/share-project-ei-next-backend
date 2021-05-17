<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Tasks;

use App\Entities\Branches\Appointment;
use App\Entities\Core\AbstractModel;
use App\Utils\DateTimeUtils;

/**
 * Class CounterTaskEvent
 * @package App\Entities\Tasks
 *
 * @property int counterTaskId
 * @property int mainTaskId
 * @property int done
 * @property int doneBy
 * @property string finishedAt
 * @property int locationEventAppointmentId
 * @property string dueAt
 *
 */
class CounterTaskEvent extends AbstractModel
{
    protected $table = 'counter_task_events';

    public function counterTask()
    {
        return $this->hasOne(CounterTask::class, 'id', 'counterTaskId');
    }

    public function mainTask()
    {
        return $this->hasOne(GenericTask::class, 'id', 'mainTaskId');
    }

    public function locationEventAppointment()
    {
        return $this->hasOne(Appointment::class, 'id', 'locationEventAppointmentId');
    }

    public function markMainTaskAsDone($userId)
    {
        /**
         * @var GenericTask $task
         */
        $task = $this->mainTask;
        if ($task != null) {
            $task->done = 1;
            $task->finishedBy = $userId;
            $task->finishedAt = DateTimeUtils::nowAsString();
            $task->save();
        }
    }


}
