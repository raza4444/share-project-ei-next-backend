<?php

/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Publics\Customers;


use App\Http\Controllers\Publics\AbstractPublicsController;
use App\Services\Customers\CustomerRegistrationService;
use Illuminate\Http\Request;

class CustomerRegistrationController extends AbstractPublicsController
{

  private $customerRegistrationService;

  public function __construct(
    CustomerRegistrationService $customerRegistrationService
  ) {
    $this->customerRegistrationService = $customerRegistrationService;
  }

  public function completeRegistration(Request $request)
  {
    try {
      $result = $this->customerRegistrationService->completeRegistration($request);
      return $this->json(200, $result);
    } catch (\Throwable $t) {
      return $this->serverErrorQuick($t->getMessage());
    }
  }
}
