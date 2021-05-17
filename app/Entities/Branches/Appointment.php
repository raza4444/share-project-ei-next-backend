<?php

/**
 * by stephan scheide
 */

namespace App\Entities\Branches;


use App\Entities\Core\AbstractModel;
use App\Entities\Core\InternUser;

/**
 * Class Appointment
 * 
 * @package App\Entities\Branches
 * @property int eventId
 * @property int appointmentTypeId
 * @property int locationId
 * @property int createdUserId
 * @property string when
 * @property Carbon finished_at
 * @property int finishedUserId
 * @property int result
 * @property string seller
 * @property int preAppointmentId
 * @property int nextAppointmentId
 * @property string preisinfo
 * @property string ansprechpartner_anrede
 * @property string ansprechpartner_vorname
 * @property string ansprechpartner_nachname
 * @property string erinnernAm
 * @property string nachgehenAm
 * @property string status
 * @property int typ
 * @property string verkauftAm
 * @property int verkauftVon
 *
 */
class Appointment extends AbstractModel
{

  const RESULT_VERKAUFT = 0;

  const RESULT_VERSCHOBEN = 20;

  const RESULT_NACHGEHEN = 30;

  const RESULT_GESCHEITERT = 90;

  const RESULT_WIDERRUF = 95;

  const TYPE_DEFAULT = 0;

  const TYPE_CONSULTANT = 1;

  protected $table = 'appointments';

  public $timestamps = false;

  public function __construct(array $attributes = [])
  {
    parent::__construct($attributes);
    $this->typ = 0;
    $this->eventId = 0;
  }

  public function event()
  {
    return $this->belongsTo(LocationEvent::class, 'eventId', 'id');
  }

  public function location()
  {
    //return $this->hasOne(Location::class,'id','locationId');
    return $this->belongsTo(Location::class, 'locationId', 'id');
  }

  public function appointmentType()
  {
    return $this->hasOne(AppointmentType::class, 'id', 'appointmentTypeId');
  }

  public function creator()
  {
    return $this->hasOne(InternUser::class, 'id', 'createdUserId');
  }

  public function canChangeResult()
  {
    return $this->result === null || $this->result == self::RESULT_NACHGEHEN || $this->result == self::RESULT_VERSCHOBEN || $this->result == self::RESULT_GESCHEITERT;
  }
}
