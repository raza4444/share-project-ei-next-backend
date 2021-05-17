<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyFollowUpInteractionType;
use App\Repositories\AbstractRepository;

class DutyFollowUpInteractionTypeRepository extends AbstractRepository
{

  public function __construct()
  {
    parent::__construct(DutyFollowUpInteractionType::class);
  }

  public function getAll()
  {
    return $this->query()->get();
  }

  public function getInteractionTypeForId($interactionTypeId) 
  {
    $interactionType = $this->query()
    ->where('id', $interactionTypeId)
    ->get(['type'])
    ->first();

    return $interactionType->type;
  }
}
