<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Branches;


use App\Entities\Core\AbstractModel;
use App\Entities\Core\InternUser;

/**
 * Class LocationEventTrack
 * @package App\Entities\Branches
 * @property int eventId
 * @property int userId
 * @property string action
 * @property string result
 * @property string notice
 * @property string appointmentAt
 * @property string appointmentTypeId
 * @property string showAgainAt
 * @property string trackedAt
 */
class LocationEventTrack extends AbstractModel
{
    protected $table = 'location_event_tracks';

    public function user()
    {
        return $this->hasOne(InternUser::class, 'id', 'userId');
    }

}
