<?php

namespace App\Http\Controllers\Intern\Branches;


use App\Http\Controllers\AbstractInternController;
use App\Services\Branches\LocationMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationMailController extends AbstractInternController
{

    private $locationMailService;

    public function __construct(LocationMailService $locationMailService)
    {
        $this->locationMailService = $locationMailService;
    }

    public function getListByLocationId(Request $request, $locationId)
    {
        $locationMail = $this->locationMailService->getListByLocationId($locationId);
        return $this->singleJson($locationMail);
    }

    public function create(Request $request)
    {   
        $data = $request->all();
        if ($validationResponse = $this->validateData($data)) {
            return $validationResponse;
        }
        return $this->locationMailService->create($data);
    }

    public function byId(Request $request, $id)
    {
        $locationMail = $this->locationMailService->byId($id);
        if ($locationMail == null) {
            return $this->notFoundWithReason('Mail not found.');
        }
        return $this->locationMailService->byId($id);
    }

    public function delete($id)
    {
        $locationMail = $this->locationMailService->byId($id);
        if(!is_null($locationMail)) {
        $locationMail = $this->locationMailService->delete($id);
        }
        return $this->noContent();
    }


    private function validateData(array $data)
    {
        $validator = Validator::make( $data, [
            'location_id' => 'required',
            'subject' => 'required',
            'content' => 'required',
            'to' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->badRequestWithReason('Request parameters is missing');
        }

        return null;
    }
}
