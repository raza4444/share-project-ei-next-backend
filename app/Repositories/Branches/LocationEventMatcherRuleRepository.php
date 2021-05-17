<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Branches;


use App\Entities\Branches\LocationEventMatcherRule;
use App\Repositories\AbstractRepository;

class LocationEventMatcherRuleRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct(LocationEventMatcherRule::class);
    }

    public function saveUniqueByCategoryId(LocationEventMatcherRule $rule)
    {
        LocationEventMatcherRule::query()->where('categoryId', '=', $rule->categoryId)->delete();
        $rule->save();
    }

}
