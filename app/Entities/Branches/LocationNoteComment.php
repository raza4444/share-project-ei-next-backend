<?php

namespace App\Entities\Branches;

use App\Entities\Core\AbstractModel;

/**
 * Class LocationNoteComment
 * @package App\Entities\Branches
 * @property int id
 * @property int locationNoteId
 * @property string userId
 * @property string content
 * @property string created_at
 */
class LocationNoteComment extends AbstractModel
{
  protected $table = 'locations_notes_comments';
  protected $fillable = [
    'locationNoteId',
    'userId',
    'content'
  ];
  protected $hidden = [
    'locationNoteId',
    'updated_at'
  ];

  public function user() 
  {
    return $this->belongsTo('App\User', 'userId', 'id');
  }

}
