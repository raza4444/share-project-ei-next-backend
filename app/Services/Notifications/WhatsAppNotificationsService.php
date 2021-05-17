<?php

/**
 * by Samuel Leicht
 */

namespace App\Services\Notifications;

/**
 * sends out pre-defined WhatsApp notifications
 * 
 * Class WhatsAppNotificationService
 */
class WhatsAppNotificationsService
{

  /**
   * 
   * sends out a given pre-defined WhatsApp notification
   * to a given (user) id by filling in the given parameters
   * 
   * @param int $id the user ID, number or secret hash code. Multiple IDs can be separated by comma.
   * @param string $notification the notification template name
   * @param array $params the params for the template placeholders
   * 
   * @return int the HTTP response code
   */
  public function sendNotification(string $id, string $notification, array $params)
  {
    $curl = curl_init(env('MESSENGER_PEOPLE_API', '') . "/chat/notification");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
      "id" => $id,
      "notification" => $notification,
      "parameters" => $params,
      "apikey" => env('MESSENGER_PEOPLE_API_KEY', '')
    ]));
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
      "Content-Type: application/json"
    ]);

    $resp = curl_exec($curl);
    $resp_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    return $resp_code;
  }
}
