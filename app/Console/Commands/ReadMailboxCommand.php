<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Branches\LocationMailService;
use Carbon\Carbon;
use Exception;
use PhpImap\Exceptions\ConnectionException;
use PhpImap\Mailbox;

class ReadMailboxCommand extends Command
{
  protected $name = 'application:read-mailbox';

  public function handle(
    LocationMailService $locationMailService
  ) {
    $date = env('IMAP_DATE', '');
    $lastEmailDate = $locationMailService->getLastIncomingEmailDate();
    if (isset($lastEmailDate) && (Carbon::parse($lastEmailDate) > Carbon::parse($date))) {
        $date = $lastEmailDate;
    }
    $date = Carbon::parse($date)->format('d M Y');
    // Create PhpImap\Mailbox instance for all further actions
    $host = '{' . env('IMAP_HOST', '') . ':' . env('IMAP_PORT', '') . '/' . env('IMAP_PROTOCOL', '') . '/' . env('IMAP_ENCRYPTION', '') . '}INBOX';
    $mailbox = new Mailbox(
        $host, // IMAP server and mailbox folder
        env('IMAP_USERNAME', ''), // Username for the before configured mailbox
        env('IMAP_PASSWORD', ''), // Password for the before configured username
        false
    );
    



    $mailsIds = array();
    try {
        $mailsIds = $mailbox->searchMailbox('SINCE "' . $date . '"');
    } catch (ConnectionException $ex) {
        die('IMAP connection failed: '.$ex->getMessage());
    } catch (Exception $ex) {
        die('An error occured: '.$ex->getMessage());
    }

    foreach ($mailsIds as $mailsId) {
        $mail = $mailbox->getMail($mailsId);
        $locationMailService->createBySender([
            "from" => $mail->fromAddress,
            "subject" => $mail->subject,
            "content" => $mail->textPlain,
            "message_id" => $mail->messageId
        ]);
        
    }

    $mailbox->disconnect();
  }
}
