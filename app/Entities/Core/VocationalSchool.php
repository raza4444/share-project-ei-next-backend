<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Core;


/**
 * Class VocationalSchool
 * @package App\Entities\Core
 * @property int userId
 * @property string info
 */
class VocationalSchool extends AbstractModel
{
    protected $table = 'vocational_school';
  protected $fillable = [
    'userId',
    'info'
  ];

  public function user()
    {
        return $this->belongsTo(InternUser::class, 'userId', 'id');
    }

    
}
