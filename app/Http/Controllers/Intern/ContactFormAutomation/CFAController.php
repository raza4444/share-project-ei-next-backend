<?php

namespace App\Http\Controllers\Intern\ContactFormAutomation;

use App\Entities\ContactFormAutomation\CFALogs;
use App\Entities\ContactFormAutomation\CFASubProcess;
use App\Http\Controllers\AbstractInternController;
use App\Services\ContactFormAutomation\CFAService;
use App\Services\Core\CurrentUserService;
use Illuminate\Http\Request;

class CFAController extends AbstractInternController
{
  private $cfaService;
  private $currentUserService;

  public function __construct(
    CFAService $cfaService,
    CurrentUserService $currentUserService
  ) {
    $this->cfaService = $cfaService;
    $this->currentUserService = $currentUserService;
  }

  /**
   * @param int $userId
   * @return void
   */
  public function start()
  {
    $token = $this->currentUserService->getCurrentApiToken();
    $result = $this->cfaService->startCFA($token);
    if ($result['success']) {
      return $this->singleJson($result);
    } else {
      return $this->accessDeniedWithReason($result['message']);
    }
  }

  /**
   * @return void
   */
  public function stop() {
    $result = $this->cfaService->stopCFA();
    if ($result['success']) {
      return $this->singleJson($result);
    } else {
      return $this->notFoundWithReason('Prozess läuft nicht');
    }
  }

  public function status()
  {
    $token = $this->currentUserService->getCurrentApiToken();
    $result = $this->cfaService->statusCFA($token);
    return $this->singleJson($result);
  }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaginatedLogs(Request $request, $logNumber)
  {
    $limit = $request->get('limit', 50);
    $offset = $request->get('offset', 0);
    $subProcess = CFASubProcess::where('log_number', $logNumber)->first();
    $data = $subProcess->cfaLogs()->limit($limit)->offset($offset)->get();

    return $this->singleJson([
      'data' => $data->toArray(),
      'total' => $subProcess->cfaLogs->count(),
      'limit' => $limit,
      'offset' => $offset,
    ]);
  }
}
