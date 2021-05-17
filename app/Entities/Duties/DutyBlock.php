<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Duties;

use App\Entities\Core\AbstractModel;

/**
 * Class DutyBlock
 * @package App\Entities\Duties
 *
 * @property string name
 */
class DutyBlock extends AbstractModel
{
    protected $fillable = ['name', 'pos'];
    protected $table = "duty_block";
}
