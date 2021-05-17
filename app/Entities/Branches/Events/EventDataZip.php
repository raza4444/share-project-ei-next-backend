<?php

namespace App\Entities\Branches\Events;

use App\Entities\Core\AbstractModel;

class EventDataZip extends AbstractModel
{
  protected $table = 'event_data_zip_codes';
  protected $fillable = ['zip'];
  protected $visible = [
    'id',
    'zip'
  ];
}
