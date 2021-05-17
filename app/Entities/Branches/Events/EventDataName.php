<?php

/**
 * by stephan scheide
 */

namespace App\Entities\Branches\Events;

use App\Entities\Core\AbstractModel;

class EventDataName extends AbstractModel
{
  protected $table = 'event_data_names';
  protected $fillable = ['name'];
  protected $visible = [
    'id',
    'name'
  ];
}
