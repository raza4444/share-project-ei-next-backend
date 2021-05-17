<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Ssl;

use App\Entities\Branches\Location;
use App\Entities\Ssl\SslJobStatus;
use App\Repositories\AbstractRepository;

class SslJobRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(Location::class);
    }

    /**
     * returns all companies with data filled for ssl job creation
     * @return Location[]
     */
    public function findLocationsReadyForCertGen()
    {
        return $this->query()
            ->whereNotNull('domain')
            ->whereNotNull('ftphost')
            ->whereNotNull('ftpusername')
            ->whereNotNull('ftppassword')
            ->whereNotNull('ftpdirectoryhtml')
            ->whereRaw('(length(domain)>2)')
            ->whereRaw('(length(ftphost)>2)')
            ->whereRaw('(length(ftpusername)>2)')
            ->whereRaw('(length(ftppassword)>2)')
            ->whereRaw('(length(ftpdirectoryhtml)>2)')
            ->whereRaw('( (agentId3 is null) or (agentId3<>137))')
            ->whereRaw('(ssl_active=1)')
            ->get();
    }

    public function findNewJobs()
    {
        return $this->query()
            ->where('status_cert_gen', '=', SslJobStatus::STATUS_NEW)
            ->where('ssl_active', '=', 1)
            ->where('ssl_origin', '=', 0)
            ->where('ftp_credentials_checked', '=', 1)
            ->get();
    }

    public function findErrorJobs()
    {
        return $this->query()
            ->where('status_cert_gen', '=', SslJobStatus::STATUS_ERROR)
            ->where('ssl_active', '=', 1)
            ->where('ssl_origin', '=', 0)
            ->where('ftp_credentials_checked', '=', 1)
            ->orderByRaw('rand()')
            ->get();
    }

    public function findSuccessfulGenJobs()
    {
        return $this->findJobs(['status_cert_gen' => SslJobStatus::STATUS_SUCCESS]);
    }

    /**
     * @param $domain
     * @return Location|null
     */
    public function findLocationByDomain($domain)
    {
        return $this->query()
            ->where('domain', '=', $domain)
            ->first();
    }

    private function filteredQuery($filter)
    {
        $q = $this->query();

        if (array_key_exists('ssl_origin', $filter)) {
            $q->where('ssl_origin', '=', $filter['ssl_origin']);
        }
        if (array_key_exists('status_cert_gen', $filter)) {
            $q->where('status_cert_gen', '=', $filter['status_cert_gen']);
        }
        if (array_key_exists('status_cert_import', $filter)) {
            $q->where('status_cert_import', '=', $filter['status_cert_import']);
        }
        if (array_key_exists('domain', $filter)) {
            $q->where('domain', '=', $filter['domain']);
        }
        if (array_key_exists('not-status_cert_gen', $filter)) {
            foreach ($filter['not-status_cert_gen'] as $s) {
                $q->whereRaw('(status_cert_gen <> ' . ($s * 1) . ')');
            }
        }
        if (array_key_exists('ssl_active', $filter)) {
            $value = $filter['ssl_active'] ? 1 : 0;
            $q->where('ssl_active', '=', $value);
        }
        if (array_key_exists('ftp_credentials_checked', $filter)) {
            $value = $filter['ftp_credentials_checked'] ? 1 : 0;
            $q->where('ftp_credentials_checked', '=', $value);
        }
        if (array_key_exists('_raw', $filter)) {
            $raws = $filter['_raw'];
            foreach ($raws as $raw) {
                $q->whereRaw($raw[0], $raw[1]);
            }
        }

        return $q;
    }

    /**
     * returns amount of matching companies
     *
     * @param $filter
     * @return integer
     */
    public function countJobs($filter = [])
    {
        $q = $this->filteredQuery($filter);
        return $q->count();
    }

    /**
     * @param $filter
     * @param null $orderBy
     * @return Location[]
     */
    public function findJobs($filter, $orderBy = null, $columns = null)
    {
        $q = $this->filteredQuery($filter);

        if ($orderBy != null) {
            $q->orderByRaw($orderBy);
        }

        return $q->get($columns == null ? ['*'] : $columns);
    }

    public function redoNeededCertGeneration()
    {
        $this->query()
            ->where('status_cert_gen', '=', SslJobStatus::STATUS_SUCCESS)
            ->whereRaw('(last_cert_gen < (now() - interval 80 day))')
            ->update(['status_cert_gen' => SslJobStatus::STATUS_NEW]);
    }

}
