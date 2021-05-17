<?php

namespace App\Entities\Core;

/**
 * Class UserAbsence
 * @package App\Entities\Core
 * @property string name
 * @property datetime created_at
 * @property datetime updated_at
 */
class Permissions extends AbstractModel
{
  protected $table = 'permissions';
  protected $fillable = [
    'name',
    'type'
  ];

  public function roles()
  {
    return $this->belongsToMany('App\Entities\Core\Roles');
  }

  
}




