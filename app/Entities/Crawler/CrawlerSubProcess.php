<?php

/**
 * by Samuel Leicht
 */

namespace App\Entities\Crawler;

use App\Entities\Core\AbstractModel;

/**
 * Class CrawlerSubProcess
 * @package App\Entities\Crawler
 *
 */
class CrawlerSubProcess extends AbstractModel
{
    protected $fillable = ['id', 'crawler_process_id', 'pid', 'type', 'status'];
    protected $table = "crawler_sub_process";
}
