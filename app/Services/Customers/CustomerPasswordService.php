<?php
/**
 * by stephan scheide
 */

namespace App\Services\Customers;


use App\Entities\Repair\RepairAble;
use App\Repositories\Customers\CustomerRepository;

class CustomerPasswordService implements RepairAble
{

    private static $passwords = [
        'hund',
        'katze',
        'huhn',
        'kind',
        'haus',
        'lila',
        'vater',
        'mutter'
    ];

    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function findRandomPassword()
    {
        $cc = count(self::$passwords);
        $i = random_int(0, $cc - 1);
        return self::$passwords[$i];
    }

    public function generateMissingPasswords($limit = 50)
    {
        $customers = $this->customerRepository->findCustomersWithoutPassword($limit);
        foreach ($customers as $c) {
            $pw = $this->findRandomPassword();
            $this->customerRepository->updatePassword($c->id, $pw);
        }
        return $customers;
    }

    public function generateMissingUsernames()
    {
        $this->customerRepository->updateMissingUsernamesToDefault();
    }

    public function writeDefaultAccessDataAndEnableAccount($id)
    {
        $pw = $this->findRandomPassword();
        $username = 'K' . $id;
        $this->customerRepository->updateLoginDataAndEnableAccount($id, $username, $pw);
    }

    public function repair()
    {
        $this->generateMissingUsernames();
        $customers = $this->generateMissingPasswords(10000);
        echo count($customers);
    }


}
