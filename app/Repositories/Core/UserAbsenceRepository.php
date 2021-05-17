<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Core;


use App\Entities\Core\UserAbsence;
use App\Entities\Core\AbsenceTypes;
use App\Repositories\AbstractRepository;

class UserAbsenceRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct(UserAbsence::class);
    }

    public function all() {

        return $this->query()->with('types')
            ->get();
    }

    public function allOfUser($userId)
    {
        return $this->query()
            ->where('userId', '=', $userId)
            ->get();
    }

    public function singleUserAbsence($id)
    {
        return $this->query()
            ->where('id', '=', $id)
            ->first();
    }
    

    public function allAffectingNow()
    {
        return $this->query()
            ->whereRaw('(CURDATE() >= `from`)')
            ->whereRaw('(CURDATE() <= `to`)')
            ->get();
    }

    public function findUserAbsenceById($id) {
       return $this->query()->find($id);
    }
    /**
     * @param array $data
     * @param int $id
     * @return void
     */
    
    public function updateUserAbsences($data , $id) {
        $userAbsence = UserAbsence::find($id);
        $userAbsence->from = $data['from'];
        $userAbsence->to = $data['to'];
        $userAbsence->am = $data['am'];
        $userAbsence->pm = $data['pm'];
        $userAbsence->type_id = $data['type_id'];
        $userAbsence->save();
        return $userAbsence;
    }

    public function getAllAbsencesTypes() {
       return AbsenceTypes::all();
    }

}