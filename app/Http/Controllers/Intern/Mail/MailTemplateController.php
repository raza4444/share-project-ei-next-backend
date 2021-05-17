<?php
namespace App\Http\Controllers\Intern\Mail;


use App\Http\Controllers\AbstractInternController;
use App\Services\Mail\MailTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MailTemplateController extends AbstractInternController
{
    private $mailTemplateService;

    public function __construct(MailTemplateService $mailTemplateService)
    {
        $this->mailTemplateService = $mailTemplateService;
    }

    public function all(Request $request)
    {
        return $this->singleJson($this->mailTemplateService->getList());
    }

    public function create(Request $request)
    {
        $data = $request->all();
        return $this->mailTemplateService->create($data);
    }

    public function byId(Request $request, $id)
    {
        $mailTemplate = $this->mailTemplateService->byId($id);
        if ($mailTemplate == null) {
            return $this->notFoundWithReason('Mail template not found.');
        }
        return $mailTemplate;
    }

    public function update(Request $request, $id)
    {
        $data = $request->json()->all();

        if ($validationResponse = $this->validateData($data)) {
            return $validationResponse;
        }

        $mailTemplate = $this->mailTemplateService->update($data, $id);

        if ($mailTemplate == null) {
            return $this->notFoundWithReason('Mail template not found.');
        }

        return $mailTemplate;
    }

    public function delete($id)
    {
        $mailTemplate = $this->mailTemplateService->byId($id);
        if(!is_null($mailTemplate)) {
        $mailTemplate = $this->mailTemplateService->delete($id);
        }
        return $this->noContent();
    }


    private function validateData(array $data)
    {
        $validator = Validator::make( $data, [
            'subject' => 'required',
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->badRequestWithReason('Request parameters is missing');
        }

        return null;
    }

    public function getMailPlaceholders() {
        return $this->mailTemplateService->getMailPlaceholders();
    }
}
