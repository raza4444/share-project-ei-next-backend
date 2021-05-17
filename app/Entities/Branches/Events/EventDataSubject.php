<?php

namespace App\Entities\Branches\Events;

use App\Entities\Core\AbstractModel;

class EventDataSubject extends AbstractModel
{
  protected $table = 'event_data_subjects';
  protected $fillable = ['subject'];
  protected $visible = [
    'id',
    'subject'
  ];
}
