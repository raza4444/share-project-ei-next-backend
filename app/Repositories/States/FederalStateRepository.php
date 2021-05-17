<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\States;


use App\Entities\States\FederalState;
use App\Repositories\AbstractRepository;

class FederalStateRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct(FederalState::class);
    }

    public function findOrCreateByNameForGermany($name)
    {
        $state = FederalState::query()
            ->where('name', '=', $name)
            ->first();

        if ($state === null) {
            $state = new FederalState();
            $state->name = $name;
            $state->save();
        }

        return $state;
    }

}
