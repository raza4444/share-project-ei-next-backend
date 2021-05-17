<?php
/**
 * by stephan scheide
 */

namespace MyTests\Integration;

use App\Entities\Core\InternUser;

class TestDataService
{

    /**
     * @param $username
     * @param $password
     * @return InternUser
     */
    public function createNormalUser($username, $password = 'test')
    {
        $user = new \App\Entities\Core\InternUser();
        $user->username = $username;
        $user->password = md5($password);
        $user->admin = 0;
        $user->save();
        return $user;
    }

    public function createAdminUser($username = 'admin', $password = 'test')
    {
        $user = new \App\Entities\Core\InternUser();
        $user->username = $username;
        $user->password = md5($password);
        $user->admin = 1;
        $user->save();
        return $user;
    }

}
