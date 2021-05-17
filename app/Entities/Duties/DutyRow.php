<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Duties;

use App\Entities\Core\AbstractModel;

/**
 * Class DutyRow
 * @package App\Entities\Duties
 * @property int dutyBlockId
 * @property int dutyRowTemplId
 * @property int appointmentId
 * @property int assignedUserId
 */
class DutyRow extends AbstractModel
{
  protected $table = "duty_rows";
}
