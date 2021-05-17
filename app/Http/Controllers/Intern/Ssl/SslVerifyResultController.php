<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Ssl;


use App\Http\Controllers\AbstractInternController;
use App\Services\Ssl\SslVerifyResultService;

class SslVerifyResultController extends AbstractInternController
{

    private $sslVerifyResultService;

    public function __construct(SslVerifyResultService $sslVerifyResultService)
    {
        $this->sslVerifyResultService = $sslVerifyResultService;
    }

    public function all()
    {
        return $this->json(200, $this->sslVerifyResultService->findAll());
    }

}
