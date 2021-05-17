<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Branches;


use App\Http\Controllers\AbstractInternController;
use App\Repositories\Branches\LocationCategoryRepository;

class LocationCategoryController extends AbstractInternController
{

    private $locationCategoryRepository;

    public function __construct(
        LocationCategoryRepository $locationCategoryRepository
    )
    {
        $this->locationCategoryRepository = $locationCategoryRepository;
    }

    public function all()
    {
        $cats = $this->locationCategoryRepository->allActiveSorted();
        return $this->json(200, $cats);
    }

}
