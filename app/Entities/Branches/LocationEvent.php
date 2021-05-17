<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Branches;


use App\Entities\Core\AbstractModel;
use App\Entities\Core\InternUser;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class LocationEvent
 * @package App\Entities\Branches
 *
 * @property Carbon timestamp
 * @property AdminUser $agent
 * @property Carbon finishedTimestamp
 * @property string $showAfter
 * @property Carbon $agentLastSeen
 * @property string $result
 * @property Location $location
 * @property CampaignTypeActive $campaignTypeActive The ActiveRange associated to this school.
 * @property int|null lockedUserId
 * @property string notiz
 * @property int arbeitskategorie
 * @property int ursprungseventId
 * @property int schoolid
 * @property Carbon shownAt
 * @property int wiedervorlage
 * @property int done
 * @property int agentLastChangeId
 * @property string ansprechpartner
 * @property int erlaubnis_anrufen
 */
class LocationEvent extends AbstractModel
{
    use SoftDeletes;

    const ARBEITSKATEGORIE_NORMAL = 0;

    const ARBEITSKATEGORIE_RUECKRUF = 1;

    public $table = "campaign_location_events";

    /**
     * @return array
     */
    public function getDates()
    {
        return array_merge(
            parent::getDates(), [
                'deleted_at',
                'doneTimestamp',
                'timestamp',
                'shownAt'
            ]
        );
    }

    public function location()
    {
        return $this->belongsTo(Location::class,'schoolId','id');
        //return $this->hasOne(Location::class, 'id', 'schoolId');
    }

    public function notes()
    {
        return $this->hasMany(LocationEventNote::class, 'eventId', 'id');
    }

    public function tracks()
    {
        return $this->hasMany(LocationEventTrack::class, 'eventId', 'id')->orderBy('created_at', 'asc');
    }

    /**
     * creates new note for this event and saves it
     *
     * @param InternUser $user
     * @param $text
     * @return LocationEventNote
     */
    public function addNote(InternUser $user, $text)
    {
        $note = new LocationEventNote();
        $note->eventId = $this->id;
        $note->userId = $user->id;
        $note->note = $text;
        $note->save();
        return $note;
    }

    public function addAndSaveNoticeIfNotEmpty(InternUser $user, $text)
    {
        if ($text == null) return null;
        $text = trim($text);
        if (strlen($text) == 0) return null;
        return $this->addNote($user, $text);
    }

}
