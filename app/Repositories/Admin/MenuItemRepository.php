<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Admin;


use App\Entities\Menus\MenuItem;
use App\Repositories\AbstractRepository;

class MenuItemRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(MenuItem::class);
    }


    
}
