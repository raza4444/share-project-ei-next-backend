<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Branches;


use App\Entities\Branches\LocationCategory;
use App\Repositories\AbstractRepository;

class LocationCategoryRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct(LocationCategory::class);
    }

    public function allActiveSorted()
    {
        return $this->query()
            ->whereNull('deleted_at')
            ->orderBy('title')
            ->get();
    }

}
