<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Branches;


use App\Entities\Core\AbstractModel;

/**
 * Class LocationEmail
 * @package App\Entities\Branches
 * @property int locationid
 * @property string email
 * @property int typ
 */
class LocationEmail extends AbstractModel
{

    const TYPE_CONTACT = 0;

    const TYPE_PRODUCT = 1;

    public $timestamps = false;

}
