<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Core;


/**
 * Class professionalSchool
 * @package App\Entities\Core
 
 * @property int userId
 * @property string info
 */
class professionalSchool extends AbstractModel
{
    protected $table = 'professional_school';
  protected $fillable = [
    'userId',
    'info'
  ];
}
