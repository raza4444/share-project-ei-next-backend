<?php
/**
 * by stephan scheide
 */

namespace App\Services\Core;


use App\Repositories\Core\UserAbsenceRepository;

class UserAbsenceService
{

    private $userAbsenceRepository;

    public function __construct(UserAbsenceRepository $userAbsenceRepository)
    {
        $this->userAbsenceRepository = $userAbsenceRepository;
    }

    /**
     * returns a facade for querying absences
     *
     * @return UserAbsenceFacade
     */
    public function createFacadeForAllAbsences()
    {
        $absences = $this->userAbsenceRepository->allAffectingNow();
        $fac = new UserAbsenceFacade();
        foreach ($absences as $a) {
            $fac->addAbsenceForNow($a);
        }
        return $fac;
    }

    /**
     * Undocumented function
     *
     * @param array $data
     * @param int $id
     * @return void
     */

    public function updateUserAbsences($data , $id) {
        $userAbsence = $this->userAbsenceRepository->updateUserAbsences($data , $id);
        return $userAbsence;
    }

    /**
     * @return void
     */
    
    public function getAllAbsencesTypes() {
        return $this->userAbsenceRepository->getAllAbsencesTypes();
    }
}
