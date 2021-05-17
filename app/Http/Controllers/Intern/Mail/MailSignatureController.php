<?php

namespace App\Http\Controllers\Intern\Mail;


use App\Http\Controllers\AbstractInternController;
use App\Services\Mail\MailSignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MailSignatureController extends AbstractInternController
{

    private $mailSignatureService;

    public function __construct(MailSignatureService $mailSignatureService)
    {
        $this->mailSignatureService = $mailSignatureService;
    }

    public function all(Request $request)
    {
        $mailSignature = $this->mailSignatureService->getList();
        return $this->singleJson($mailSignature);
    }

    public function create(Request $request)
    {   
        $data = $request->all();
         if($this->mailSignatureService->isNameExist($data['name'])) {
            return  $this->conflictWithReason('Unterschrift mit diesem Namen .$data["name"]. existiert bereits.');
         }
        return $this->mailSignatureService->create($data);
    }

    public function byId(Request $request, $id)
    {
        $mailSignature = $this->mailSignatureService->byId($id);
        if ($mailSignature == null) {
            return $this->notFoundWithReason('Mail template not found.');
        }
        return $this->mailSignatureService->byId($id);
    }

    public function update(Request $request, $id)
    {
        $data = $request->json()->all();

        if ($validationResponse = $this->validateData($data)) {
            return $validationResponse;
        }

        $mailSignature = $this->mailSignatureService->update($data, $id);

        if ($mailSignature == null) {
            return $this->notFoundWithReason('Mail template not found.');
        }

        return $mailSignature;
    }

    public function delete($id)
    {
        $mailSignature = $this->mailSignatureService->byId($id);
        if(!is_null($mailSignature)) {
        $mailSignature = $this->mailSignatureService->delete($id);
        }
        return $this->noContent();
    }


    private function validateData(array $data)
    {
        $validator = Validator::make( $data, [
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->badRequestWithReason('Request parameters is missing');
        }

        return null;
    }
}
