<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Publics\Ssl;


use App\Entities\Ssl\SslJobStatus;
use App\Http\Controllers\Publics\AbstractPublicsController;
use App\Services\Ssl\SslService;
use App\Utils\DateTimeUtils;
use Illuminate\Http\Request;

class SslJobsController extends AbstractPublicsController
{

    const SECRET_TOKEN = '8c5462116d668c2a39c5f6e097990497d1ca5fcfdf7496fe8b3d16e230315ffc3d29d1f0938adda120f3fa75ee0c752522199f3722fc307ef59b7865afda96f7';

    private $sslService;

    public function __construct(SslService $sslService)
    {
        $this->sslService = $sslService;
    }

    public function getJobs(Request $request)
    {

        if (!$this->hasBearerInHeader($request, self::SECRET_TOKEN)) {
            return $this->ourResponse(null, 401);
        }

        $filter = [];

        if ($request->has('status_cert_gen')) {
            $filter['status_cert_gen'] = $request->get('status_cert_gen') * 1;
        }
        if ($request->has('status_cert_import')) {
            $filter['status_cert_import'] = $request->get('status_cert_import') * 1;
        }
        if ($request->has('domain')) {
            $filter['domain'] = $request->get('domain');
        }

        $locations = $this->sslService->findJobs($filter);

        foreach ($locations as $loc) {
            $this->sslService->addFileContentsToJob($loc);
            $loc->privatekeyfilecontent = base64_encode($loc->privatekeyfilecontent);
            $loc->fullchainfilecontent = base64_encode($loc->fullchainfilecontent);
        }

        return $this->json(200, $locations);

    }

    public function changeState(Request $request, $id)
    {
        //status_cert_gen
        //status_cert_import
        //error
        //options

        if (!$this->hasBearerInHeader($request, self::SECRET_TOKEN)) {
            return $this->ourResponse(null, 401);
        }

        $loc = $this->sslService->findByIdId($id);
        if ($loc == null) {
            return $this->notFound();
        }

        $now = DateTimeUtils::nowAsString();

        $all = $request->json()->all();

        if (array_key_exists('status_cert_gen', $all)) {
            $loc->status_cert_gen = $all['status_cert_gen'] * 1;
            $loc->last_cert_gen_touched = $now;
            $loc->ssl_count_processed_gen++;
        }
        if (array_key_exists('status_cert_import', $all)) {
            $loc->status_cert_import = $all['status_cert_import'] * 1;
            $loc->last_cert_import_touched = $now;
            if ($loc->status_cert_import == SslJobStatus::STATUS_SUCCESS) {
                $loc->last_cert_import = $now;
            }
            $loc->ssl_count_processed_import++;
        }

        if (array_key_exists('options', $all)) {
            $loc->ssl_options = $all['options'];
        }

        if (array_key_exists('error', $all)) {
            $loc->last_ssl_error = $now;
            $loc->last_ssl_error_message = $all['error'];
        }

        $loc->save();

        return $this->singleJson($loc);

    }

}
