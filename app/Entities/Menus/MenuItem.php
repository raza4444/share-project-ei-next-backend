<?php

/**
 * Created by PhpStorm.
 * User: kingster
 * Date: 16.12.2018
 * Time: 18:02
 */

namespace App\Entities\Menus;

use App\Entities\Core\AbstractModel;
use App\Entities\Core\Permissions;

/**
 * Class MenuItem
 * @package App\Entities\Menus
 *
 * @property int menuId
 * @property string titel
 * @property int nummer
 * @property string schluessel
 * @property int sichtbar
 * @property int permission_id
 * @property string routerlink
 *
 */
class MenuItem extends AbstractModel
{
    protected $table = 'menuitems';
    protected $fillable = ['routerlink', 'schluessel', 'permission_id', 'titel', 'sichtbar', 'menuId', 'nummer'];

    public function permissions()
    {
        return $this->belongsTo(Permissions::class, 'permission_id', 'id');
    }
}
