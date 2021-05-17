<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Customers;

use App\Entities\Core\AbstractModel;

/**
 * Class CustomerInfoData
 * @package App\Entities\Customers
 * @property int customerid
 * @property int type
 * @property string name
 * @property string value
 */
class CustomerInfoData extends AbstractModel
{

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'customer_infodata';

    const TYPE_DEFAULT = 0;

    const TYPE_REGISTRATION = 10;

    const TYPE_REGISTRATION_RAW = 11;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->type = self::TYPE_DEFAULT;
    }


}
