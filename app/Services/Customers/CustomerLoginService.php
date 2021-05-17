<?php
/**
 * by stephan scheide
 */

namespace App\Services\Customers;


use App\Entities\Customers\Customer;
use App\Repositories\Customers\CustomerRepository;
use App\Repositories\Customers\CustomerTokenRepository;

class CustomerLoginService
{

    private $customerRepository;

    private $customerTokenRepository;

    public function __construct(
        CustomerTokenRepository $customerTokenRepository,
        CustomerRepository $customerRepository
    )
    {
        $this->customerRepository = $customerRepository;
        $this->customerTokenRepository = $customerTokenRepository;
    }

    /**
     * @param $username
     * @param $password
     * @return \App\Entities\Customers\Customer|null
     */
    public function loginByUsernameAndPassword($username, $password)
    {
        //Benutzername ist immer K$id
        if ($username[0] != 'k' && $username[0] != 'K') return null;

        $id = substr($username, 1);

        return $this->customerRepository->findByIdAndPassword($id, $password);
    }

    /**
     * @param Customer $customer
     * @return string
     * @throws \Exception
     */
    public function generateTokenForCustomer(Customer $customer)
    {

        $this->clearOldCustomerTokens();

        $str = date('YmdHis') . $customer->id;
        $bytes = random_bytes(32);
        $str .= bin2hex($bytes);

        $ct = $this->customerTokenRepository->createForCustomer($customer->id, $str);
        return $ct->token;
    }

    private function clearOldCustomerTokens()
    {
        $this->customerTokenRepository->clearOldCustomerTokens();
    }


}
