<?php
/**
 * by stephan scheide
 */

namespace App\Services\Core;


use App\Entities\Core\ApiToken;
use App\Entities\Core\InternUser;
use Carbon\Carbon;

class ApiTokenService
{

    public function deleteToken($token)
    {
        ApiToken::query()->where('token', '=', $token)->delete();
    }

    public function findUserByToken($token)
    {
        $t = ApiToken::byToken($token);
        return $t == null ? null : $t->user;
    }

    /**
     *
     * erzeugt neuen API token
     *
     * @param InternUser $user
     * @return ApiToken
     */
    public function createApiTokenForUser(InternUser $user)
    {
        $this->cleanOldTokens();
        $str = $user->id . date('YmdHis');

        $bytes = random_bytes(16);
        $str .= bin2hex($bytes);

        $token = new ApiToken();
        $token->userid = $user->id;
        $token->token = $str;
        $token->save();

        return $token;
    }

    private function cleanOldTokens()
    {
        $fines = Carbon::now()->addDays(-30);
        ApiToken::query()
            ->where('created_at', '<', $fines)
            ->delete();
    }

}
