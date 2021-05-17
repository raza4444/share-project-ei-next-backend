<?php

/**
 * by stephan scheide
 */

namespace App\Entities\Branches\Events;

use App\Entities\Core\AbstractModel;

class EventDataNumber extends AbstractModel
{
  protected $table = 'event_data_numbers';
  protected $fillable = ['number'];
  protected $visible = [
    'id',
    'number'
  ];
}
