<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Appointments;


use App\Entities\Core\AbstractModel;

/**
 * Class PossibleAppointmentsAmount
 * @package App\Entities\Appointments
 *
 * @property int appointmentTypeId
 * @property int hour
 * @property int minute
 * @property int amount
 * @property string monday
 * @property int weekday
 * @property int default
 */
class PossibleAppointmentsAmount extends AbstractModel
{
    protected $table = 'possible_appointments_amount';

    protected $primaryKey = null;

    public $timestamps = false;

    public $incrementing = false;
}
