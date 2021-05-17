<?php
/**
 * by stephan scheide
 */

namespace App\Services\Core;


use App\Entities\Core\InternUser;

class CurrentUserState
{
    public static $user = null;

    public static $apiToken = null;
}

class CurrentUserService
{

    public function assignCurrentUser(InternUser $user, $currentApiToken)
    {
        CurrentUserState::$user = $user;
        CurrentUserState::$apiToken = $currentApiToken;
    }

    public function getCurrentApiToken()
    {
        return CurrentUserState::$apiToken;
    }

    /**
     * @return InternUser
     */
    public function getCurrentUser()
    {
        $u = CurrentUserState::$user;
        if ($u == null) {
            throw new \Exception("Kein Benutzer im Kontext gefunden");
        }
        return $u;
    }

}
