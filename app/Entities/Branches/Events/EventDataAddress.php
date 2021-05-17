<?php

namespace App\Entities\Branches\Events;

use App\Entities\Core\AbstractModel;

class EventDataAddress extends AbstractModel
{
  protected $table = 'event_data_addresses';
  protected $fillable = ['address'];
  protected $visible = [
    'id',
    'address'
  ];
}
