<?php

namespace App\Services\Crawler;

use App\Entities\Crawler\CrawlerData;
use App\Entities\Crawler\CrawlerProcess;
use App\Entities\Crawler\CrawlerTypes;
use App\Repositories\Crawler\CrawlerRepository;

class CrawlerService
{

  /**
   * @var CrawlerRepository
   */
  private $crawlerRepository;

  /**
   * @param CrawlerRepository $crawlerRepository
   */
  public function __construct(
    CrawlerRepository $crawlerRepository
  ) {
    $this->crawlerRepository = $crawlerRepository;
  }


  /**
   * @param [array] $urls
   * @param [int] $userId
   * @param [int] $crawlerProcessId
   * @return void
   */

  public function addUrls(array $urls, int $userId): int
  {
    $crawlerProcess = new CrawlerProcess([
      'user_id' => $userId,
      'status' => 0,
      'type' => CrawlerTypes::DOMAIN_CRAWLER
    ]);
    $crawlerProcess->save();
    $crawlerProcessId = $crawlerProcess->id;

    foreach ($urls as $url) {
      if (isset($url)) {
        $crawlerData = CrawlerData::where('link', $url);
        if ($crawlerData->count() === 0) {

          $crawlerDataObj = new CrawlerData([
            'link' => $url,
            'user_id' => $userId,
            'crawler_process_id' => $crawlerProcessId,
            'type' => CrawlerTypes::DOMAIN_CRAWLER
          ]);

          $crawlerDataObj->save();
        } else {
          $crawlerData = $crawlerData->first();
          CrawlerData::where('id', $crawlerData->id)
            ->update(['is_visited' => 0, 'user_id' => $userId, 'crawler_process_id' => $crawlerProcessId, 'type' => CrawlerTypes::DOMAIN_CRAWLER]);
        }
      }
    }

    return $crawlerProcessId;
  }

  public function getDataWithCellNo()
  {
    return $this->crawlerRepository->getDataWithCellNo();
  }

  public function checkCellNo($data, $id)
  {
    return $this->crawlerRepository->checkCellNo($data, $id);
  }

  /**
   * @param string $type
   * @return array
   */

  public function getBatchId(string $type)
  {
    return $this->crawlerRepository->getMaxBatchId($type);
  }

  /**
   * @param string $type
   * @return array
   */

  public function getAllBatchId(string $type)
  {
    return $this->crawlerRepository->getAllBatchId($type);
  }
}
