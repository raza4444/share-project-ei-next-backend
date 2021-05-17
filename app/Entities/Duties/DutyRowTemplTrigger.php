<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Duties;

use App\Entities\Core\AbstractModel;

/**
 * Class DutyRowTemplTrigger
 * 
 * @package App\Entities\Duties
 *
 * @property int dutyRowTemplId
 * @property int dutyTriggerId
 */
class DutyRowTemplTrigger extends AbstractModel
{
    protected $fillable = ['dutyRowTemplId', 'dutyTriggerId'];
    protected $table = "duty_rows_templ_trigger";
}
