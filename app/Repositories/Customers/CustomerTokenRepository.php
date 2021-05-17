<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Customers;


use App\Entities\Customers\CustomerToken;
use App\Utils\DateTimeUtils;
use Carbon\Carbon;

class CustomerTokenRepository
{

    public function createForCustomer($customerId, $token)
    {
        $c = new CustomerToken();
        $c->customerid = $customerId;
        $c->token = $token;
        $c->created_at = DateTimeUtils::nowAsString();
        $c->save();
        return $c;
    }

    public function findByToken($token)
    {
        return CustomerToken::query()
            ->where('token', '=', $token)
            ->with('customer')
            ->first();
    }

    public function clearOldCustomerTokens()
    {
        $fines = Carbon::now()->addDays(-30);
        CustomerToken::query()
            ->where('created_at', '<', $fines)->delete();
    }


}
