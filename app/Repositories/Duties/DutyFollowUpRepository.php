<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyFollowUp;
use App\Entities\Core\PermissionType;
use App\Repositories\AbstractRepository;

class DutyFollowUpRepository extends AbstractRepository
{

  public function __construct()
  {
    parent::__construct(DutyFollowUp::class);
  }

  public function getFollowUpsForTaskTempl($blockId, $dutyRowTemplId, $dutyTaskTemplId)
  {
    return $this->query()
      ->where('duty_follow_ups.dutyBlockId', $blockId)
      ->where('duty_follow_ups.dutyRowTemplId', $dutyRowTemplId)
      ->where('duty_follow_ups.dutyTaskTemplId', $dutyTaskTemplId)
      ->leftJoin('duty_blocks_rows_templ', 'duty_blocks_rows_templ.id', 'duty_follow_ups.targetDutyBlockRowTemplId')
      ->get([
        'duty_follow_ups.id',
        'duty_follow_ups.dutyBlockId',
        'duty_follow_ups.dutyRowTemplId',
        'duty_follow_ups.dutyTaskTemplId',
        'duty_follow_ups.followUpDutyRowTemplId',
        'duty_follow_ups.followUpDutyTaskTemplId',
        'duty_follow_ups.followUpInteractionTypeId',
        'duty_follow_ups.interactionMsg',
        'duty_follow_ups.targetDutyBlockId',
        'duty_blocks_rows_templ.dutyRowTemplId AS targetDutyRowTemplId',
        'duty_follow_ups.sameRowCreation'
      ]);
  }

  public function getRawFollowUp($dutyFollowUpId)
  {
    return $this->query()
      ->where('id', $dutyFollowUpId)
      ->get()
      ->first();
  }

  public function deleteFollowUp($dutyFollowUpId)
  {
    return $this->query()
      ->where('id', $dutyFollowUpId)
      ->delete();
  }

  /**
   * @return array
   */
  public function getDutyConfiguratorFollowUpPermissions()
  {
    return [
      PermissionType::DUTY_CONFIGURATOR_FOLLOWUP_ACTION_SHOW,
      PermissionType::DUTY_CONFIGURATOR_FOLLOWUP_ACTION_ADD,
      PermissionType::DUTY_CONFIGURATOR_FOLLOWUP_ACTION_EDIT,
      PermissionType::DUTY_CONFIGURATOR_FOLLOWUP_ACTION_DELETE
    ];
  }
}
