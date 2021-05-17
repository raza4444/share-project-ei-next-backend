<?php

/**
 * by stephan scheide
 */

namespace App\Entities\Branches\Events;

use App\Entities\Core\AbstractModel;

class EventDataFollowUpCaption extends AbstractModel
{
  protected $table = 'event_data_follow_up_captions';
  protected $fillable = ['caption', 'order', 'campaign_type'];
  protected $visible = [
    'id',
    'caption',
    'campaign_type',
    'order',
    'segmentValues'
  ];

  public function segmentValues()
  {
    return $this->belongsToMany(EventDataSegmentValue::class, 'event_data_fu_cap_seg_val', 'follow_up_caption_id', 'seg_val_id')
      ->withPivot('order')
      ->orderBy('pivot_order', 'asc');
  }
}
