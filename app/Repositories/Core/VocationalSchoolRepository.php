<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Core;


use App\Entities\Core\VocationalSchool;
use App\Entities\Core\AbsenceTypes;
use App\Repositories\AbstractRepository;
use App\Entities\Core\PermissionType;

class VocationalSchoolRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct(VocationalSchool::class);
    }

    public function all() {

        return $this->query()->with('user')
            ->get();
    }

    public function byId($id)
    {
        return $this->query()
            ->where('id', '=', $id)
            ->first();
    }
    
/**
     * @param array $data
     * @param int $id
     * @return void
     */
    
    public function update($data , $id) {
        $professionalSchool = VocationalSchool::find($id);
        $professionalSchool->info = $data['info'];
        $professionalSchool->save();
        return $professionalSchool;
    }

    public function getVocationalSchoolSchedulePermissions()
    {
      return [
        PermissionType::VOCATIONAL_SCHOOL_SCHEDULE_SHOW,
        PermissionType::VOCATIONAL_SCHOOL_SCHEDULE_ADD,
        PermissionType::VOCATIONAL_SCHOOL_SCHEDULE_EDIT,
        PermissionType::VOCATIONAL_SCHOOL_SCHEDULE_DELETE
      ];
    }
}