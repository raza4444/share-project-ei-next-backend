<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Branches;


use App\Entities\Core\AbstractModel;
use App\Entities\Core\InternUser;

/**
 * Class LocationEventNotes
 * @package App\Entities\Branches
 * @property int userId
 * @property int eventId
 * @property string note
 */
class LocationEventNote extends AbstractModel
{

    public $table = "location_event_notes";

    public function user()
    {
        return $this->hasOne(InternUser::class, 'id', 'userId');
    }


}
