<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Duties;

use App\Entities\Core\AbstractModel;

/**
 * Class DutyTask
 * @package App\Entities\Duties
 * @property int dutyRowId
 * @property int dutyTaskTemplId
 * @property boolean done
 * @property DateTime updated_at
 */
class DutyTask extends AbstractModel
{
    protected $fillable = ['dutyRowId', 'dutyTaskTemplId'];
    protected $table = "duty_rows_tasks";
}
