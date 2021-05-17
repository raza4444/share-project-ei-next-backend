<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Admin;


use App\Entities\Core\InternUser;
use App\Repositories\AbstractRepository;

class InternUserRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct(InternUser::class);
    }

    /**
     * Liefert einen Benutzer mittels NarevID
     * @param $narevUserId
     * @return InternUser
     */
    public function byNarevId($narevUserId)
    {
        return $this->query()
            ->whereNull('deleted_at')
            ->where('narev_id', '=', $narevUserId)
            ->first();
    }

    public function byUsername($username)
    {
        return $this->query()->where('username', '=', $username)->first();
    }

    public function findMainAdmin()
    {
        return $this->byUsername('admin');
    }

}