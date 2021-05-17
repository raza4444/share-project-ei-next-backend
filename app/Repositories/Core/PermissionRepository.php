<?php
namespace App\Repositories\Core;

use App\Entities\Core\Permissions;
use App\Entities\Core\PermissionType;
use App\Repositories\AbstractRepository;

class PermissionRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct(Permissions::class);
    }

    /**
     * @return array
     */

    public function all() {

        return $this->query()
            ->get();
    }

    /**
     * @return array
     */

    public function allForSpecificType(string $type) {
        return $this->query()->where('type', '=', $type)->get();
    }

    /**
     * @param string $columnName
     * @return array
     */

    public function getSpecificColumn($columnName)  {
        return $this->query()
            ->pluck($columnName);
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
     * @return array
     */
    public function getUserAbsencePermissions() {
        return [
            PermissionType::USER_ABSENCE_SHOW_TABLE,
            PermissionType::USER_ABSENCE_SHOW_EXTEND_TABLE,
            PermissionType::USER_ABSENCE_ADD,
            PermissionType::USER_ABSENCE_EDIT,
            PermissionType::USER_ABSENCE_DELETE
        ];
    }

    /**
     * @param string $permissionName
     * @param string $type
     * @return void
     */
    public function createPermission(string $permissionName , $type = null) {
        $newPermission = new Permissions();
        $newPermission->name = $permissionName;
        $newPermission->type = $type;
        $newPermission->save();
        return $newPermission->id;
    }

    /**
     * @param string $oldPermissionName
     * @param string $type
     * @param string $newPermission
     * @return void
     */
    public function updateByName(string $oldPermissionName ,string $type = null , string $newPermission)  {
        Permissions::where('name', $oldPermissionName)->update(['name' => $newPermission]);
    }

    /**
     * @param string $permissionName
     * @param string $type
     * @return Permissions
     */
    public function findByName(string $permissionName ,string $type = null)  {
      return Permissions::where(['name'=> $permissionName, 'type'=> $type] )->first();
    }



    /**
     * @param string $permissionName
     * @param string $type
     * @return void
     */

    public function deleteByName(string $permissionName, string $type) {
        Permissions::where(['name'=>$permissionName, 'type'=>$type])->delete();
    }
}