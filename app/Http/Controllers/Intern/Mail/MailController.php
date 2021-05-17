<?php

namespace App\Http\Controllers\Intern\Mail;


use App\Http\Controllers\AbstractInternController;
use App\Services\Branches\LocationMailService;
use App\Services\Mail\MailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use PhpImap\Exceptions\ConnectionException;
use PhpImap\Mailbox;

class MailController extends AbstractInternController
{

    private $mailService;
    private $locationMailService;

    public function __construct(MailService $mailService, LocationMailService $locationMailService)
    {
        $this->mailService = $mailService;
        $this->locationMailService = $locationMailService;
    }

    public function sendEmail(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make( $data, [
            'to' => 'required',
            'subject' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->badRequestWithReason('Request parameters is missing');
        }
        return $this->mailService->sendEmail($data);
    }
}