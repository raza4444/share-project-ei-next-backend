<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Duties;

use App\Entities\Core\AbstractModel;

/**
 * Class DutyRowTempl
 * @package App\Entities\Duties
 * @property int dutyBlockId
 * @property string name
 * @property string description
 * @property boolean createOnce
 * @property int minFinishedTasksToCompl
 */
class DutyRowTempl extends AbstractModel
{
    protected $fillable = ['dutyBlockId', 'name', 'description', 'createOnce', 'minFinishedTasksToCompl'];
    protected $table = "duty_row_templates";
}
