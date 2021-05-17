<?php

namespace App\Entities\Branches;

use App\Entities\Core\AbstractModel;

/**
 * Class AppointmentType
 * 
 * @package App\Entities\Branches
 * @property int id
 * @property string name
 * @property string display_name
 *
 */
class AppointmentType extends AbstractModel
{
  const TYPE_SALES_APPOINTMENT = 1;
  const TYPE_GENERAL_APPOINTMENT = 1;
  const TYPE_TECHNICAL_APPOINTMENT = 1;
  const TYPE_FOLLOW_UP_APPOINTMENT = 1;

  protected $table = 'appointment_types';
}
