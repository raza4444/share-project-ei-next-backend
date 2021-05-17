<?php

/**
 * Created by PhpStorm.
 * InternUser: kingster
 * Date: 16.12.2018
 * Time: 15:34
 */

namespace App\Entities\Core;

use App\Services\Core\UserPermissionFacade;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class InternUser
 * @package App\Entities\Core
 *
 * @property string username
 * @property int user_role_id
 * @property int admin
 * @property string password
 * @property string last_action_at
 * @property int narev_id
 * @property string narev_token
 *
 */
class InternUser extends AbstractModel implements AuthenticatableContract, AuthorizableContract
{
    use SoftDeletes;
    use Authenticatable;
    use Authorizable;

    protected $fillable = ['username', 'user_role_id', 'admin', 'password', 'end_greeting'];

    protected $table = 'users';

    public function updateLastActionToNow()
    {
        $this->last_action_at = date('Y-m-d H:i:s');
        $this->save();
    }

    public function permissionFacade()
    {
        return new UserPermissionFacade($this);
    }

    public function roles()
    {
        return $this->belongsTo(Roles::class, 'user_role_id', 'id')->with('permissions');
    }

    public function individualPermissions()
    {
        return $this->belongsToMany(Permissions::class, 'user_permission', 'user_id', 'permission_id');
    }
}
