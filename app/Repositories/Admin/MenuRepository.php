<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Admin;


use App\Entities\Menus\Menu;
use App\Repositories\AbstractRepository;

class MenuRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(Menu::class);
    }
}
