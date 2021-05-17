<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Ssl;


use App\Entities\Ssl\SslJobStatus;
use App\Http\Controllers\AbstractInternController;
use App\Services\Ssl\SslService;
use App\Utils\StringUtils;
use Illuminate\Http\Request;

class SslStatController extends AbstractInternController
{

    private $sslService;

    public function __construct(SslService $sslService)
    {
        $this->sslService = $sslService;
    }

    /**
     * returns a counter map for quick statistics information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function counts()
    {
        $arr = [
            'all' => $this->sslService->countJobs(),
            'active' => $this->sslService->countJobs(['ssl_active' => 1]),
            'ours' => $this->sslService->countJobs(['ssl_origin' => 0]),
            'ftp_credentials_checked' => $this->sslService->countJobs(['ftp_credentials_checked' => 1]),
            'inactive' => $this->sslService->countJobs(['ssl_active' => 0]),
            'success_gen' => $this->sslService->countJobs(['status_cert_gen' => SslJobStatus::STATUS_SUCCESS]),
            'success_import' => $this->sslService->countJobs(['status_cert_import' => SslJobStatus::STATUS_SUCCESS]),
            'error_gen' => $this->sslService->countJobs(['status_cert_gen' => SslJobStatus::STATUS_ERROR]),
            'error_import' => $this->sslService->countJobs(['status_cert_import' => SslJobStatus::STATUS_ERROR]),
            'error' => $this->sslService->countJobs(['_raw' => [['(status_cert_gen=? or status_cert_import=?)', [SslJobStatus::STATUS_ERROR, SslJobStatus::STATUS_ERROR]]]])
        ];

        return $this->json(200, $arr);
    }

    /**
     * returns information about locations matching the domain
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function domains(Request $request)
    {
        $domain = $request->get('domain');
        if (StringUtils::isTooShort($domain, 3)) {
            return $this->badRequestWithReason('domain-too-short');
        }
        $jobs = $this->sslService->findJobs(['domain' => $domain]);
        foreach ($jobs as $loc) {
            $this->sslService->addAdditionalSslInfoToLocation($loc);
            $this->sslService->checkLocation($loc);
        }
        return $this->json(200, $jobs);
    }

    /**
     * marks whole ssl-process to be restarted
     *
     * @param $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function restart($id)
    {
        $id = StringUtils::ensureInteger($id);
        if ($id == 0) {
            return $this->badRequestWithReason('invalid-id');
        }
        $result = $this->sslService->restartWholeSslForLocationId($id);
        return $result ? $this->noContent() : $this->serverError('internal-error');
    }

    /**
     * marks fields and triggers a new import of given certificate file
     *
     * @param $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function reimport($id)
    {
        $id = StringUtils::ensureInteger($id);
        if ($id == 0) {
            return $this->badRequestWithReason('invalid-id');
        }
        $result = $this->sslService->forceImportById($id);
        return $result ? $this->noContent() : $this->serverError('internal-error');
    }

}
