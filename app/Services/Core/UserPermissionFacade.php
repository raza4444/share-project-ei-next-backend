<?php

/**
 * by stephan scheide
 */

namespace App\Services\Core;


use App\Entities\Core\InternUser;

class UserPermissionFacade
{

  private $user;

  private $permissions;

  public function __construct(InternUser $user)
  {
    $this->user = $user;
    $this->parsePermissions($user->permissions);
  }

  public function canAccessItem($name)
  {
    if ($this->user->admin == 1) {
      return true;
    }

    if (strlen($name) >= 2) {
      $master = substr($name, 0, 1);
      if (in_array($master, $this->permissions)) {
        return true;
      }
    }

    if (in_array($name, $this->permissions)) {
      return true;
    }
    return false;
  }

  private function parsePermissions($str)
  {
    $this->permissions = [];
    if ($str === null) {
      return;
    }
    $str = str_replace(' ', '', $str);
    $values = explode(',', $str);
    $this->permissions = $values;
  }
}
