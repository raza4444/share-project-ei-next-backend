<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Publics\Customers;


use App\Http\Controllers\Publics\AbstractPublicsController;
use App\Services\Customers\CustomerLoginService;
use Illuminate\Http\Request;

class CustomerLoginController extends AbstractPublicsController
{

    private $customerLoginService;

    public function __construct(
        CustomerLoginService $customerLoginService
    )
    {
        $this->customerLoginService = $customerLoginService;
    }

    public function login(Request $request)
    {

        $username = $request->get('username');
        $password = $request->get('password');

        $c = $this->customerLoginService->loginByUsernameAndPassword($username, $password);

        if ($c == null) {
            return $this->accessDenied();
        }

        $token = $this->customerLoginService->generateTokenForCustomer($c);

        return $this->singleJson([
            'id' => $c->id,
            'token' => $token,
            'registerlink' => $c->registerlink
        ]);

    }

}