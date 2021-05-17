<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyBlockRowTempl;
use App\Repositories\AbstractRepository;

class DutyBlockRowTemplRepository extends AbstractRepository
{

  public function __construct()
  {
    parent::__construct(DutyBlockRowTempl::class);
  }

  public function deleteWhere($blockId, $rowId)
  {
    $this->query()->where('dutyBlockId', '=', $blockId)->where('dutyRowTemplId', '=', $rowId)->delete();
  }

  public function getIdForBlockRowCombination($targetBlockId, $targetRowTemplId)
  {
    $blockRowTemplComb = $this->query()
      ->where('dutyBlockId', $targetBlockId)
      ->where('dutyRowTemplId', $targetRowTemplId)
      ->get(['id'])
      ->first();

    return $blockRowTemplComb['id'];
  }
}
