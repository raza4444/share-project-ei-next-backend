<?php

namespace App\Entities\Branches;

use App\Entities\Core\AbstractModel;

/**
 * Class LocationNote
 * @package App\Entities\Branches
 * @property int location_id
 * @property int user_id
 * @property string title
 * @property string content
 * @property int pos_x
 * @property int pos_y
 * @property boolean pinned
 * @property string updated_at
 */
class LocationNote extends AbstractModel
{
  public $timestamps = false;
  protected $table = 'locations_notes';
  protected $fillable = [
    'locationId',
    'userId',
    'title',
    'content',
    'posX',
    'posY',
    'pinned',
    'updated_at',
    'location_mail_id'
  ];
  protected $hidden = [
    'locationId',
    'userId'
  ];

  public function user() 
  {
    return $this->belongsTo('App\User', 'userId', 'id');
  }

  public function comments() 
  {
    return $this->hasMany('App\Entities\Branches\LocationNoteComment', 'locationNoteId', 'id');
  }

  public function mail()
  {
      return $this->belongsTo(LocationMail::class, 'location_mail_id', 'id');
  }

}
