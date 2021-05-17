<?php

/**
 * by stephan scheide
 */

namespace App\Entities\Branches\Events;

use App\Entities\Core\AbstractModel;

class EventDataMail extends AbstractModel
{
  protected $table = 'event_data_mails';
  protected $fillable = ['mail', 'pw_info'];
  protected $visible = [
    'id',
    'mail',
    'pw_info'
  ];
}
