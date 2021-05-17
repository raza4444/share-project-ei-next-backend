<?php

namespace App\Entities\Branches\Events;

use App\Entities\Core\AbstractModel;

class EventDataGreetingName extends AbstractModel
{
  protected $table = 'event_data_greeting_names';
  protected $fillable = ['greeting_name'];
  protected $visible = [
    'id',
    'greeting_name'
  ];
}
