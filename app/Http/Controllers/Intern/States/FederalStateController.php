<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\States;


use App\Http\Controllers\AbstractInternController;
use App\Repositories\States\FederalStateRepository;

class FederalStateController extends AbstractInternController
{

    private $federalStateRepository;

    public function __construct(
        FederalStateRepository $federalStateRepository
    )
    {
        $this->federalStateRepository = $federalStateRepository;
    }

    public function all()
    {
        return $this->json(200, $this->federalStateRepository->all());
    }

}
