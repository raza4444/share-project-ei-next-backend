<?php
/**
 * by stephan scheide
 */

namespace App\Entities\Customers;


use App\Entities\Branches\Location;

/**
 *
 * Ein Kunde ist ein Unternehmen
 * Vererberung hier, normalerweise, wenn es geht, mit Delegation arbeiten
 *
 * Class Customer
 * @package App\Entities\Customers
 */
class Customer extends Location
{

    const STATE_NEW = 0;

    const STATE_CUSTOMER = 10;

    const STATE_FORMER_CUSTOMER = 20;

}
