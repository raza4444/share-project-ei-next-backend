<?php

/**
 * by stephan scheide
 */

namespace App\Entities\Branches\Events;

use App\Entities\Core\AbstractModel;

class EventData extends AbstractModel
{
  protected $table = 'event_data';
  protected $fillable = [
    'company_register_id',
    'event_data_names_id',
    'event_data_greeting_names_id',
    'event_data_mails_id',
    'event_data_numbers_id',
    'event_data_customer_number',
    'event_data_subjects_id',
    'event_data_zip_codes_id',
    'event_data_addresses_id',
    // 'event_data_follow_up_captions_id',
    'event_data_cur_user_id'
  ];
  protected $hidden = [
    'id',
    'created_at',
    'updated_at'
  ];

  public function eventDataName()
  {
    return $this->hasOne('App\Entities\Branches\Events\EventDataName', 'id', 'event_data_names_id');
  }

  public function eventDataGreetingName()
  {
    return $this->hasOne('App\Entities\Branches\Events\EventDataGreetingName', 'id', 'event_data_greeting_names_id');
  }

  public function eventDataMail()
  {
    return $this->hasOne('App\Entities\Branches\Events\EventDataMail', 'id', 'event_data_mails_id');
  }

  public function eventDataNumber()
  {
    return $this->hasOne('App\Entities\Branches\Events\EventDataNumber', 'id', 'event_data_numbers_id');
  }

  public function eventDataSubject()
  {
    return $this->hasOne('App\Entities\Branches\Events\EventDataSubject', 'id', 'event_data_subjects_id');
  }

  public function eventDataZip()
  {
    return $this->hasOne('App\Entities\Branches\Events\EventDataZip', 'id', 'event_data_zip_codes_id');
  }

  public function eventDataAddress()
  {
    return $this->hasOne('App\Entities\Branches\Events\EventDataAddress', 'id', 'event_data_addresses_id');
  }

  // public function eventDataFollowUpCaption()
  // {
  //   return $this->hasOne('App\Entities\Branches\Events\EventDataFollowUpCaption', 'id', 'event_data_follow_up_captions_id');
  // }

  public function eventDataSegmentValues()
  {
    return $this->belongsToMany(EventDataSegmentValue::class, 'event_data_event_data_seg_val', 'event_data_id', 'seg_val_id');
  }

  public function eventDataCurUser()
  {
    return $this->hasOne('App\Entities\Core\InternUser', 'id', 'event_data_cur_user_id');
  }

}
