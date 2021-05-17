<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Publics\Branches;

use App\Entities\Branches\IncomingCompany;
use App\Entities\Branches\ValidationException;
use App\Http\Controllers\Publics\AbstractPublicsController;
use App\Repositories\Branches\IncomingCompanyRepository;
use App\Services\Branches\IncomingCompanyTransformService;
use Illuminate\Http\Request;

/**
 * rest api for importaing companies into narev system
 *
 * Class PublicCompanyController
 */
class PublicCompanyController extends AbstractPublicsController
{


    private $incomingCompanyRepository;

    private $incomingCompanyTransformService;

    public function __construct(
        IncomingCompanyRepository $incomingCompanyRepository,
        IncomingCompanyTransformService $incomingCompanyTransformService
    )
    {
        $this->incomingCompanyRepository = $incomingCompanyRepository;
        $this->incomingCompanyTransformService = $incomingCompanyTransformService;
    }

    public function test() {
        echo "OK";
    }

    /**
     * transforms single important company into a school
     * remark: schools will intern perform a special dublicate check by using phone number
     *
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createRealSingleCompany($id)
    {
        $result = $this->incomingCompanyTransformService->transformSingleImportedCompanyById($id);
        if ($result === 0) {
            return $this->notFound();
        } else if ($result instanceof School) {
            return $this->json(200, $result);
        }
    }

    /**
     * if called, all non transformed imported companies will be transformed into schools
     * remark: schools will intern perform a special dublicate check by using phone number
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function createRealCompanies()
    {
        $companies = $this->incomingCompanyRepository->findAllNonTransformed();
        $manyResult = [];
        foreach ($companies as $c) {
            $txt = 'success';
            try {
                $result = $this->incomingCompanyTransformService->transformSingleImportedCompany($c);
                if ($result === 0) {
                    $txt = 'notFound';
                } else {
                    $txt = 'success';
                }
            } catch (\Exception $e) {
                $txt = 'error - ' . $e->getMessage();
            }
            $manyResult[$c->id] = $txt;
        }

        return $this->json(203, $manyResult);
    }

    /**
     * imports company by request body
     * if transform=1, it will be transformed into a school immediatly!
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function importSingle(Request $request)
    {

        $doTransform = $request->get('transform') == 1;

        $names = ['name', 'name2', 'bundesland', 'land', 'branche', 'strasse', 'plz', 'ort', 'oeffnungszeiten', 'webseite',
            'telefonnummer', 'email', 'inhaber', 'erzeuger'];

        $company = IncomingCompany::createCommonInstance();

        foreach ($names as $name) {
            $company->$name = $request->get($name);
        }

        try {
            $company->saveNoDuplicate();

            if ($doTransform) {
                $this->incomingCompanyTransformService->transformSingleImportedCompany($company);
            }

            return $this->json(200, $company);
        } catch (ValidationException $e) {
            return $this->badRequest($e->getMessage());
        }

    }

}
