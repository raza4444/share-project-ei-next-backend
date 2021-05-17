<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Duties;

use App\Entities\Core\AbstractModel;

/**
 * Class DutyRowTempl
 * @package App\Entities\Duties
 * @property int rowTemplId
 * @property int columnId
 * @property int colDateTypeId
 * @property int colDateOffset
 * @property int colValueId
 */
class DutyRowTemplColumn extends AbstractModel
{
  protected $fillable = ['rowTemplId', 'columnId', 'colDateTypeId', 'colDateOffset', 'colValueId'];
  protected $table = "duty_rows_templ_cols";
}
