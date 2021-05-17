<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Crawler;

use App\Entities\Core\AbstractModel;

/**
 * Class CrawlerData
 * @package App\Entities\Crawler
 *
 */
class CrawlerData extends AbstractModel
{
  protected $fillable = ['link', 'user_id', 'crawler_process_id', 'type', 'in_queue' ,'is_crawling'];
  protected $table = "crawler_data";

  public function subUrls()
  {
    return $this->hasMany('App\Entities\Crawler\CrawlerDataChild', 'crawler_data_id', 'id');
  }

  public function keywordresults()
  {
    return $this->hasMany('App\Entities\Crawler\CrawlerKeywordResult', 'crawler_data_id', 'id');
  }
}
