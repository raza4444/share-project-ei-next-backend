<?php
/**
 * by stephan scheide
 */

namespace App\Services\Ssl;


use App\Entities\Ssl\SslVerifyResult;
use App\Repositories\Ssl\SslVerifyResultRepository;

class SslVerifyResultService
{

    private $sslVerifyResultRepository;

    public function __construct(SslVerifyResultRepository $sslVerifyResultRepository)
    {
        $this->sslVerifyResultRepository = $sslVerifyResultRepository;
    }

    public function clear()
    {
        $this->sslVerifyResultRepository->deleteAll();
    }

    public function saveDomainResult(SslVerifyResult $result)
    {
        $this->sslVerifyResultRepository->saveDomainResult($result);
    }

    /**
     * @return SslVerifyResult[]
     */
    public function findInvalid()
    {
        $filter = ['valid' => 0];
        return $this->sslVerifyResultRepository->find($filter);
    }

    public function findAll()
    {
        return $this->sslVerifyResultRepository->find([]);
    }
}
