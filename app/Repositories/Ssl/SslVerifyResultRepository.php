<?php
/**
 * by stephan scheide
 */

namespace App\Repositories\Ssl;


use App\Entities\Ssl\SslVerifyResult;

class SslVerifyResultRepository
{

    public function deleteAll()
    {
        SslVerifyResult::query()->delete();
    }

    public function saveDomainResult(SslVerifyResult $r)
    {
        $this->deleteByDomain($r->domain);
        $r->save();
        return $r;
    }

    public function deleteByDomain($domain)
    {
        SslVerifyResult::query()->where('domain', '=', $domain)->delete();
    }

    public function find($filter)
    {
        $q = SslVerifyResult::query();
        if (is_array($filter)) {
            if (array_key_exists('valid', $filter)) {
                $q->where('valid', '=', $filter['valid']);
            }
        }
        return $q->get();
    }

}
