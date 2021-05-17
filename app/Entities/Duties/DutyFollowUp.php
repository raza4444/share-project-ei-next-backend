<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Duties;

use App\Entities\Core\AbstractModel;

/**
 * Class DutyFollowUp
 * 
 * @package App\Entities\Duties
 *
 * @property int dutyBlockId
 * @property int dutyRowTemplId
 * @property int dutyTaskTemplId
 * @property int followUpDutyTaskTemplId
 * @property int followUpDutyRowTemplId
 * @property int targetDutyBlockId
 * @property int targetDutyBlockRowTemplId
 * @property boolean sameRowCreation
 * @property string interactionMsg
 * @property int followUpInteractionTypeId
 */
class DutyFollowUp extends AbstractModel
{
  protected $fillable = [
    'dutyBlockId',
    'dutyRowTemplId',
    'dutyTaskTemplId',
    'followUpDutyTaskTemplId',
    'followUpDutyRowTemplId',
    'targetDutyBlockId',
    'targetDutyBlockRowTemplId',
    'sameRowCreation',
    'interactionMsg',
    'followUpInteractionTypeId'
  ];

  protected $table = "duty_follow_ups";
}
