<?php
/**
 * by stephan scheide
 */

namespace App\Servers;


use App\Entities\Core\InternUser;
use App\Repositories\Core\UserRepository;

class CachingUserAccess
{

    private $userRepository;

    private $users = [];

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param $userId
     * @return InternUser|null
     */
    public function byId($userId)
    {
        if (array_key_exists($userId, $this->users)) {
            return $this->users[$userId];
        }

        $user = $this->userRepository->byIdActive($userId);
        $this->users[$userId] = $user;
        return $user;
    }

}
