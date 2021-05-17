<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Crawler;

use App\Entities\Core\AbstractModel;

/**
 * Class CrawlerKeyword
 * @package App\Entities\Crawler
 *
 */
class CrawlerKeyword extends AbstractModel
{
  protected $fillable = ['keyword', 'section', 'report_result'];
  protected $table = "crawler_keywords";
}
