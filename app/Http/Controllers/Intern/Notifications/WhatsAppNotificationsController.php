<?php

/**
 * by Samuel Leicht
 */

namespace App\Http\Controllers\Intern\Notifications;

use App\Http\Controllers\AbstractInternController;
use App\Services\Notifications\WhatsAppNotificationsService;
use Illuminate\Http\Request;

class WhatsAppNotificationsController extends AbstractInternController
{
  private $whatsAppNotificationsService;

  public function __construct(
    WhatsAppNotificationsService $whatsAppNotificationsService
  ) {
    $this->whatsAppNotificationsService = $whatsAppNotificationsService;
  }
  
  public function sendNotification(Request $req)
  {
    if($req->has('id') && $req->has('notification')) {

      $params = [];

      if($req->has('params')) {
        $params = $req->get('params');
      }

      $res = $this->whatsAppNotificationsService->sendNotification(
        $req->get('id'),
        $req->get('notification'),
        $params
      );
      return response('', $res);
    }
    return response('', 400);
  }
}