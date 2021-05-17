<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Duties;

use App\Entities\Core\AbstractModel;

/**
 * Class DutyTask
 * @package App\Entities\Duties
 * @property string name
 * @property string description
 */
class DutyTaskTempl extends AbstractModel
{
    protected $fillable = ['name', 'description'];
    protected $table = "duty_task_templates";
}
