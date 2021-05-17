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
 */
class DutyColumnDateType extends AbstractModel
{
  protected $table = "duty_column_date_types";
}
