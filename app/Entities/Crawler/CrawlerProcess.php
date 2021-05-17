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
class CrawlerProcess extends AbstractModel
{
  protected $fillable = ['id', 'pid', 'user_id', 'type', 'status'];
  protected $table = "crawler_process";

  public function crawlerSubProcess()
  {
    return $this->hasMany('App\Entities\Crawler\CrawlerSubProcess', 'crawler_process_id');
  }
}
