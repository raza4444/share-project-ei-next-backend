<?php
/**
 * by stephan scheide
 */

namespace App\Services\Core;


use App\Repositories\Core\VocationalSchoolRepository;

class VocationalSchoolService
{

    private $vocationalSchoolRepository;

    public function __construct(VocationalSchoolRepository $vocationalSchoolRepository)
    {
        $this->vocationalSchoolRepository = $vocationalSchoolRepository;
    }

    /**
     * @param array $data
     * @param int $id
     * @return void
     */

    public function update($data , $id) {
        $userAbsence = $this->vocationalSchoolRepository->update($data , $id);
        return $userAbsence;
    }
}
