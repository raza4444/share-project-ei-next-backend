<?php
namespace App\Services\Mail;
use Illuminate\Support\Facades\Mail;

class MailService {

    public function __construct() {}

     /**
     * Send an e-mail.
     *
     * @param  $data
     * @return Response
     */
    public function sendEmail($data)
    {
        Mail::raw($data['content'], function ($message) use($data) {
            $message->to($data['to'], null);
            $message->subject($data['subject']);
        });

        return $data;
    }

     /**
     * Send an e-mail.
     *
     * @param  $data
     * @return array
     */
    public function sendReplyEmail($data)
    {
        Mail::raw($data['content'], function ($message) use($data) {
            $message->to($data['to'], null)->replyTo($data['to'], null)->subject($data['subject']);
        });

        return $data;
    }

}