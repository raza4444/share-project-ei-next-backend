<?php

/**
 * by stephan scheide
 */

namespace App\Entities\Core;


/**
 * Class UserAbsence
 * @package App\Entities\Core
 * @property string name
 * @property boolean admin
 * @property datetime created_at
 * @property datetime updated_at
 */
class Roles extends AbstractModel
{
  protected $table = 'roles';
  protected $fillable = [
    'name'
  ];

  public function permissions()
  {
    return $this->belongsToMany('App\Entities\Core\Permissions', 'role_permission' , 'user_role_id', 'user_permission_id');
  }
}

