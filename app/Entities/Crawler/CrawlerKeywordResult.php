<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Crawler;

use App\Entities\Core\AbstractModel;

/**
 * Class CrawlerKeywordResult
 * @package App\Entities\Crawler
 *
 */
class CrawlerKeywordResult extends AbstractModel
{
  protected $table = "crawler_keyword_result";

  public function keyword()
  {
    return $this->belongsTo('App\Entities\Crawler\CrawlerKeyword', 'keyword_id', 'id');
  }
}
