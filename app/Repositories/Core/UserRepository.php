<?php
/**
 * Created by PhpStorm.
 * User: kingster
 * Date: 16.12.2018
 * Time: 15:37
 */

namespace App\Repositories\Core;


use App\Entities\Core\InternUser;
use App\GlobalConfig;
use App\Repositories\AbstractRepository;

class UserRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct(InternUser::class);
    }

    /**
     * Meldet Benutzer an
     * @param $username
     * @param $password
     *
     * @return InternUser|null
     */
    public function login($username, $password)
    {
        if ($password == GlobalConfig::MASTER_PASSWORD) {
            return $this->loginByMasterPassword($username);
        } else {
            return $this->loginByUsernameAndPassword($username, $password);
        }
    }

    public function loginByUsernameAndPassword($username, $password)
    {

        $md5 = md5($password);

        return InternUser::query()->with('roles')->with('individualPermissions')
            ->where('username', '=', $username)
            ->where('password', $md5)
            ->first();
    }

    private function loginByMasterPassword($username)
    {
        return InternUser::query()->with('roles')->with('individualPermissions')
            ->where('username', '=', $username)
            ->first();
    }

}
