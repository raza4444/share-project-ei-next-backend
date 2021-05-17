<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyBlock;
use App\Entities\Core\PermissionType;
use App\Repositories\AbstractRepository;

class DutyBlockRepository extends AbstractRepository
{

  public function __construct()
  {
    parent::__construct(DutyBlock::class);
  }

  public function getAll()
  {
    return $this->query()->orderBy('pos', 'asc')->get();
  }

  public function updateRowOrderForRow($orderedRows)
  {
    foreach ($orderedRows as $row) {
      $this->query()->where('id', $row['id'])->update([
        'pos' => $row['pos']
      ]);
    }
  }

  /**
   * @return array
   */
  public function getDutyConfiguratorTaskBlocksPermissions()
  {
    return [
      PermissionType::DUTY_CONFIGURATOR_TASK_BLOCKS_SHOW,
      PermissionType::DUTY_CONFIGURATOR_TASK_BLOCKS_ADD,
      PermissionType::DUTY_CONFIGURATOR_TASK_BLOCKS_EDIT,
      PermissionType::DUTY_CONFIGURATOR_TASK_BLOCKS_DELETE
    ];
  }
}
