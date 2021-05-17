<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Customers;


use App\Http\Controllers\AbstractInternController;
use App\Repositories\Customers\CustomerRepository;

class CustomerController extends AbstractInternController
{

    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getAllDomainConfiguration()
    {
        $items = $this->customerRepository->findCustomersDomainConfiguration();
        return $this->json(200, $items);
    }

}
