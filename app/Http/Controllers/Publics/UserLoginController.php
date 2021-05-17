<?php

/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Publics;

use App\Entities\Core\ApiToken;
use App\Logging\AppLogger;
use App\Logging\CL;
use App\Repositories\Core\UserRepository;
use App\Services\Core\ApiTokenService;
use App\Services\Core\NarevUserService;
use Illuminate\Http\Request;

class UserLoginController extends AbstractPublicsController
{

  private $userRepository;

  private $apiTokenService;

  private $narevUserService;

  public function __construct(
    ApiTokenService $apiTokenService,
    NarevUserService $narevUserService,
    UserRepository $userRepository
  ) {
    $this->apiTokenService = $apiTokenService;
    $this->userRepository = $userRepository;
    $this->narevUserService = $narevUserService;
  }

  public function postLogin(Request $request)
  {
    CL::setCurrent(new AppLogger('userlogin'));

    $username = $request->get('username');
    $password = $request->get('password');

    CL::debug('input: ' . $username . ' ' . $password);

    $internUser = $this->userRepository->login($username, $password);
    if ($internUser === null) {
      CL::debug('not found --> access denied');
      return $this->accessDenied();
    } else {
      $token = $this->apiTokenService->createApiTokenForUser($internUser)->token;

      //Frage Narev, ob Zugriff moeglich ist
      $narevId = $internUser->narev_id;
      CL::debug('narevId ' . $narevId);
      $canAccess = $narevId && $narevId > 0 ? $this->narevUserService->canUserAccessEi($narevId) : true;
      CL::debug('canAccess ' . ($canAccess ? 'yes' : 'no'));

      return $this->singleJson(
        [
          'apitoken' => $token,
          'canaccess' => $canAccess,
          'user' => $internUser
        ]
      );
    }
  }

  public function postLogout($userId)
  {
    ApiToken::where('userid', $userId)->delete();
    return $this->noContent();
  }
}
