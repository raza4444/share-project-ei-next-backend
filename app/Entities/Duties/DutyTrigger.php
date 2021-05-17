<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Duties;

use App\Entities\Core\AbstractModel;

/**
 * Class DutyTrigger
 * 
 * @package App\Entities\Duties
 *
 * @property string name
 * @property boolean appointment_creation
 * @property boolean location_creation_w_reminder
 */
class DutyTrigger extends AbstractModel
{
    protected $table = "duty_triggers";
}
