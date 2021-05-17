<?php

/**
 * Created by PhpStorm.
 * User: kingster
 * Date: 16.12.2018
 * Time: 15:47
 */

namespace App\Http\Controllers\Admin\Users;


use App\Entities\Core\InternUser;
use App\Http\Controllers\AbstractInternController;
use App\Services\Core\RoleService;
use Illuminate\Http\Request;

class UsersController extends AbstractInternController
{
    private $userAbsencesController;
    private $roleService;

    public function __construct(
        UserAbsencesController $userAbsencesController,
        RoleService $roleService
    ) {
        $this->userAbsencesController = $userAbsencesController;
        $this->roleService = $roleService;
    }

    public function all()
    {
        $users = InternUser::with('individualPermissions')->get();
        return $this->singleJson($users);
    }

    public function create(Request $request)
    {
        /**
         * @var $user InternUser
         */
        $user = $this->jsonAsEntity($request, InternUser::class);
        $user->id = null;

        // Set admin = 1 if linked user role is administrators
        $user = $this->makeUserAdmin($user);

        $user->password = md5($request->get('password'));
        $user->save();
        return $this->entityCreated($user);
    }

    public function update(Request $request, $id)
    {
        $data = $request->json()->all();

        $user = InternUser::where('id', $id)->first();
        if ($user == null) {
            return $this->notFound();
        }

        $user->username = $data['username'];
        $user->user_role_id = $data['user_role_id'];
        $user->end_greeting = $data['end_greeting'];

        if (array_key_exists('password', $data)) {
            $user->password = md5($data['password']);
        }

        // Set admin = 1 if linked user role is administrators
        $user = $this->makeUserAdmin($user);

        $user->save();

        return InternUser::where('id', $id)->with('individualpermissions')->first();
    }

    public function deactivate($id)
    {
        $user = InternUser::find($id);

        if ($user == null) return $this->notFound();

        $user->delete();

        $this->userAbsencesController->deleteAllOfUser($id);

        return $this->noContent();
    }

    public function byId($id)
    {
        $user = InternUser::where('id', $id)->first();
        return $user == null ? $this->notFound() : $this->singleJson($user);
    }

    public function updateManyUsers(Request $request)
    {
        /**
         * @var InternUser $user
         */
        $userData = $request->json()->all();

        $result = [];

        foreach ($userData as $data) {
            $id = $data['id'] * 1;
            $user = InternUser::where('id', $id)->first();
            if ($user == null) continue;

            $user->permissions = $data['permissions'];
            $user->save();

            $user->password = null;

            $result[] = $user;
        }

        return $this->singleJson($result);
    }

    public function updateIndividualPermissions(Request $request, $userId)
    {
        $permissions = $request->all();
        $userToUpdate = InternUser::where('id', $userId)->first();

        if (isset($userToUpdate)) {

            $userToUpdate->individualPermissions()
                ->sync(array_map(function ($p) {
                    return array_key_exists('id', $p) ? $p['id'] : null;
                }, $permissions));

            $updatedUser = InternUser::where('id', $userId)->with('individualPermissions')->first();
            return $updatedUser->individualPermissions;
        } else {
            return $this->notFound();
        }
    }

    /**
     * Sets the admin property according to the user role
     * 
     * @param InternUser $userObj the user object to modify
     * @return InternUser the modified user object
     */
    private function makeUserAdmin($userObj)
    {
        // Set admin = 1 if linked user role is administrators
        $userRole = $this->roleService->find($userObj->user_role_id);
        if ($userRole && $userRole['admin'] === 1) {
            $userObj->admin = 1;
        } else if ($userRole && $userRole['admin'] === 0) {
            $userObj->admin = 0;
        }
        return $userObj;
    }
}
