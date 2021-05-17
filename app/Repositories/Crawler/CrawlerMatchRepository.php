<?php

namespace App\Repositories\Crawler;

use App\Entities\Crawler\CrawlerData;
use App\Entities\Crawler\CrawlerKeyword;
use App\Entities\Crawler\CrawlerKeywordResult;

class CrawlerMatchRepository
{
    public function __construct()
    {
    }

    /**
     * @return array|null
     */
    public function getKeywords()
    {
        return CrawlerKeyword::all();
    }

    /**
     * @param int $id
     * @return object|null
     */
    public function getKeyword($id)
    {
        return CrawlerKeyword::findOrFail($id);
    }

    /**
     * @param int $id
     * @param array $updatedKeyword the keyword to be updated
     * @return object|null
     */
    public function updateKeyword($id, $updatedKeyword)
    {
        $keyword = CrawlerKeyword::findOrFail($id);
        $keyword->update($updatedKeyword);
        $keyword->save();
        return $keyword;
    }

    /**
     * @param int $id
     */
    public function deleteKeyword($id)
    {
        if (CrawlerKeywordResult::where('keyword_id', '=', $id)->count() > 0) {
            CrawlerKeywordResult::where('keyword_id', '=', $id)->delete();
        }
        CrawlerKeyword::findOrFail($id)->delete();
    }

    /**
     * Returns a maximum of 20 urls that contain lock criteria or matched keywords
     * @return array|null
     */
    public function getResultsWithLockCriteriaOrMatchedKeywords()
    {
        return CrawlerData::with('keywordresults')
            ->with('keywordresults.keyword')
            ->with('subUrls')
            ->where('type', '=', 'url-dashboard')
            ->where('is_visited', 1)
            // ->where('is_invalid_url', 0)
            ->where('checked_results_at', null)
            ->where(function ($q) {
                $q->where('has_search', 1)
                    ->orWhere('has_shop', 1)
                    ->orWhere('has_newsletter', 1)
                    ->orWhereHas('keywordresults', function ($q) {
                        $q
                            ->where('result', 1)
                            ->whereHas('keyword', function ($q) {
                                $q->where('report_result', '<>', '')
                                    ->whereNotNull('report_result');
                            });
                    })
                    ->orWhereHas('subUrls', function ($q) {
                        $q->where('has_search', 1)
                            ->orWhere('has_shop', 1)
                            ->orWhere('has_newsletter', 1);
                    });
            })
            ->limit(20)
            ->get();
    }

    /**
     * @param array $keywordObj
     * @return object|null
     */
    public function addKeyword($keywordObj)
    {
        $keyword = new CrawlerKeyword($keywordObj);
        $keyword->save();
        return $keyword;
    }
}
