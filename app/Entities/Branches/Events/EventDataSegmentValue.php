<?php

namespace App\Entities\Branches\Events;

use App\Entities\Core\AbstractModel;

class EventDataSegmentValue extends AbstractModel
{
  protected $table = 'event_data_segment_values';
  protected $fillable = ['segment_id', 'value'];
  protected $visible = [
    'id',
    'segment_id',
    'value'
  ];
}
