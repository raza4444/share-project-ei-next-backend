<?php

/**
 * by stephan scheide
 */

namespace App\Entities\Core;


use Carbon\Carbon;

/**
 * Class ApiToken
 * @property string token
 * @property int userid
 * @property InternUser user
 *
 * @package App\Entities\Core
 */
class ApiToken extends AbstractModel
{
  protected $table = 'apitokens';

  public static function byToken($token)
  {

    $fines = Carbon::now()->addDays(-30);

    return ApiToken::query()
      ->where('token', '=', $token)
      ->where('created_at', '>=', $fines)
      ->first();
  }

  public function user()
  {
    return $this->hasOne(InternUser::class, 'id', 'userid')->with('roles')->with('individualPermissions');
  }
}
