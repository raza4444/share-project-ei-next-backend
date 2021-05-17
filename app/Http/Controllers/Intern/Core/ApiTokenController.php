<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Core;


use App\Http\Controllers\AbstractInternController;
use App\Services\Core\ApiTokenService;
use App\Services\Core\CurrentUserService;

class ApiTokenController extends AbstractInternController
{

    private $currentUserService;

    private $apiTokenService;

    public function __construct(
        ApiTokenService $apiTokenService,
        CurrentUserService $currentUserService
    )
    {
        $this->apiTokenService = $apiTokenService;
        $this->currentUserService = $currentUserService;
    }

    public function delete($token)
    {
        if ($token != $this->currentUserService->getCurrentApiToken()) {
            return $this->accessDeniedWithReason('not-your-token');
        }
        $this->apiTokenService->deleteToken($token);
        return $this->noContent();
    }

    public function getUserByToken($token)
    {
        if ($token != $this->currentUserService->getCurrentApiToken()) {
            return $this->accessDeniedWithReason('not-your-token');
        }
        return $this->singleJson($this->apiTokenService->findUserByToken($token));
    }

}
