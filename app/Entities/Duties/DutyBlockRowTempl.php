<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Duties;

use App\Entities\Core\AbstractModel;

/**
 * Class DutyBlockRowTempl
 * @package App\Entities\Duties
 *
 * @property int dutyBlockId
 * @property int dutyRowTemplId
 * @property int pos
 */
class DutyBlockRowTempl extends AbstractModel
{
    protected $fillable = ['dutyBlockId', 'dutyRowTemplId', 'pos'];
    protected $table = "duty_blocks_rows_templ";
}
