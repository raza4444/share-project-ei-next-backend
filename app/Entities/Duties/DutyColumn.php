<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Duties;

use App\Entities\Core\AbstractModel;

/**
 * Class DutyColumnType
 * @package App\Entities\Duties
 * @property int id
 * @property string name
 * @property string type
 * @property boolean default
 */
class DutyColumn extends AbstractModel
{
  protected $table = "duty_columns";
}
