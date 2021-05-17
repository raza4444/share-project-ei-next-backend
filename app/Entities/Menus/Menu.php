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
 * Class Menu
 * @package App\Entities\Menus
 *
 * @property string titel
 * @property int nummer
 * @property string schluessel
 * @property int sichtbar
 * @property int permission_id
 *
 */
class Menu extends AbstractModel
{
    protected $table = 'menus';

    protected $fillable = ['titel', 'nummer', 'schluessel', 'permission_id', 'sichtbar'];

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menuid', 'id')->with('permissions')->orderBy('nummer', 'asc');
    }

    public function permissions()
    {
        return $this->belongsTo(Permissions::class, 'permission_id', 'id');
    }
}
