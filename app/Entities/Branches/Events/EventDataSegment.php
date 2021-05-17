<?php

namespace App\Entities\Branches\Events;

use App\Entities\Core\AbstractModel;

class EventDataSegment extends AbstractModel
{
  protected $table = 'event_data_segments';
  protected $fillable = [];
  protected $visible = [
    'id',
    'running_number',
    'values'
  ];

  public function values() 
  {
    return $this->hasMany(EventDataSegmentValue::class, 'segment_id');
  }
}
