<?php

namespace App\Repositories\Crawler;

use App\Entities\Crawler\CrawlerData;
use App\Entities\Crawler\CrawlerTypes;

class CrawlerResultRepository
{
    public function __construct()
    {
    }

    /**
     * @param int $limit
     * @return array|null
     */
    public function getUrlCrawlerResultsForResultsList($limit, $batchId)
    {
        if($batchId == 0) {
        return CrawlerData::with('keywordresults', 'keywordresults.keyword')
            ->with('subUrls')
            ->where('is_visited', 1)
            // ->where('is_invalid_url', 0)
            ->where(function ($query) {
                $query->where('type', '=', CrawlerTypes::DASHBOARD_CRAWLER)
                    ->orWhereNull('type');
            })
            ->orderBy('scanned_at', 'desc')
            ->limit($limit)
            ->get();
        } else {
            return CrawlerData::with('keywordresults', 'keywordresults.keyword')
            ->with('subUrls')
            ->where('is_visited', 1)
            ->where('batch_id', $batchId)
            // ->where('is_invalid_url', 0)
            ->where(function ($query) {
                $query->where('type', '=', CrawlerTypes::DASHBOARD_CRAWLER)
                    ->orWhereNull('type');
            })
            ->orderBy('scanned_at', 'desc')
            ->limit($limit)
            ->get();
        }
    }

    /**
     * @param int $limit
     * @return array|null
     */
    public function getContactFormCrawlerResultsForResultsList($limit, $batchId)
    {
        if($batchId === 0) {
            return CrawlerData::where([
                'is_visited' => 1,
                // 'is_invalid_url' => 0,
                'crawler_process_id' => null,
                'type' => CrawlerTypes::CONTACT_FORM_CRAWLER
            ])
                ->orderBy('scanned_at', 'desc')
                ->limit($limit)
                ->get();

        } else {
            return CrawlerData::where([
                'is_visited' => 1,
                'batch_id' => $batchId,
                // 'is_invalid_url' => 0,
                'crawler_process_id' => null,
                'type' => CrawlerTypes::CONTACT_FORM_CRAWLER
            ])
                ->orderBy('scanned_at', 'desc')
                ->limit($limit)
                ->get();
        }

        
    }

    /**
     * @param string $type
     * @param string $domain
     * @return array|null
     */
    public function getCrawlerResultsForDomain($type, $domain)
    {
        return CrawlerData::with('keywordResults', 'keywordResults.keyword', 'subUrls')
            ->where('link', $domain)
            ->where('is_visited', 1)
            ->where('type', $type)
            // ->where('is_invalid_url', 0)
            ->get();
    }

    /**
     * @param CrawlerData $keywordObj
     * @param int $id (optional)
     * @return object|null
     */
    public function updateResult($resultObj, $id)
    {
        if ($id) {
            $result = CrawlerData::findOrFail($id);
            $result->update($resultObj);
            $result->save();
            return $result;
        }

        $resultObj->save();
        return $resultObj;
    }
}
