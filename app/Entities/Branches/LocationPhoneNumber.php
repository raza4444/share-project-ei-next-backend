<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Branches;


use App\Entities\Core\AbstractModel;

/**
 * Class LocationPhoneNumber
 * @package App\Entities\Branches
 *
 * @property int locationid
 * @property string phonenumber
 */
class LocationPhoneNumber extends AbstractModel
{

    protected $table = 'location_phonenumbers';

    public $timestamps = false;

}
