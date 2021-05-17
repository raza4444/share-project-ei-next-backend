<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Customers;


use App\Entities\Core\AbstractModel;

/**
 * Class CustomerToken
 * @package App\Entities\Customers
 * @property string token
 * @property int customerid
 * @property string created_at
 *
 */
class CustomerToken extends AbstractModel
{

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customerid');
    }

}
