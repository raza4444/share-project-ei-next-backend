<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Core;


/**
 * Class UserAbsence
 * @package App\Entities\Core
 * @property string from
 * @property string to
 * @property int userId
 * @property boolean am
 * @property boolean pm
 * @property boolean type_id
 */
class UserAbsence extends AbstractModel
{
    public function types()
    {
        return $this->belongsTo(AbsenceTypes::class, 'type_id', 'id');
    }
}
