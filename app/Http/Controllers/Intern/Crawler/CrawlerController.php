<?php

namespace App\Http\Controllers\Intern\Crawler;

use App\Entities\Crawler\CrawlerData;
use App\Http\Controllers\AbstractInternController;
use App\Services\Crawler\CrawlerService;
use App\Services\Crawler\CrawlerBackgroundProcessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Entities\Crawler\CrawlerTypes;
use App\Repositories\Crawler\CrawlerRepository;

class CrawlerController extends AbstractInternController
{
  private $crawlerBackgroundProcessService;
  private $crawlerService;

  public function __construct(
    CrawlerBackgroundProcessService $crawlerBackgroundProcessService,
    CrawlerService $crawlerService,
    MatchController $matchController,
    CrawlerRepository $crawlerRepository
  ) {
    $this->crawlerBackgroundProcessService = $crawlerBackgroundProcessService;
    $this->crawlerService = $crawlerService;
    $this->matchController = $matchController;
    $this->crawlerRepository = $crawlerRepository;
  }

  public function getQueue()
  {
    return CrawlerData::where('in_queue', 1)
      ->where(function ($query) {
        $query->where('type', '=', CrawlerTypes::DASHBOARD_CRAWLER)
          ->orWhereNull('type');
      })
      ->get();
  }

  public function getContactFormCrawlerQueue()
  {
    return CrawlerData::where(['in_queue' => 1, 'type' => CrawlerTypes::CONTACT_FORM_CRAWLER])->get();
  }

  public function deleteUrl($urlId)
  {
    $url = CrawlerData::findOrFail($urlId);
    $url->subUrls()->delete();
    $url->delete();
    return $this->noContent();
  }
  
  /**
   * @param Request $request
   * @return array
   */

  public function addUrls(Request $request)
  {
    $urls = $request->all();
    $urlsToSave = $saveUrlData = [];

    $existingUrls = CrawlerData::whereIn('link', $urls)->distinct('link')->pluck('link')->toArray(); 
    $batchId = $this->crawlerService->getBatchId(CrawlerTypes::DASHBOARD_CRAWLER) ?? 0;
    $batchId = $batchId + 1;
    if(isset($existingUrls) && count($existingUrls) > 0) {
    
      CrawlerData::whereIn('link', $urls)->update([
        'in_queue' => 1,
        'is_crawling' => 0,
        'is_invalid_url' => 0,
        'type' => CrawlerTypes::DASHBOARD_CRAWLER,
        'crawler_process_id' => null,
        'user_id' => null,
        'batch_id'=>$batchId
      ]);
    
    }

    $urlsToSave = array_diff($urls, $existingUrls);
    
    foreach ($urlsToSave as $url) {
      $saveUrlData[] =  [
        'link' => $url,
        'in_queue' => 1,
        'type' => CrawlerTypes::DASHBOARD_CRAWLER,
        'batch_id'=>$batchId
      ];
    }

    CrawlerData::insert($saveUrlData);

    return $urls;
  }

  public function importCSVToAddUrls(Request $request) {
    
    $validator = Validator::make($request->all(), [
      'crawler_links_file' => 'required|file',
  ]);
  
  if ($validator->fails()) {
    return $this->badRequestWithReason('Request parameters is missing');
  }
    $path = $request->file('crawler_links_file')->getRealPath();
    $crawlerData = array_map('str_getcsv', file($path));
    
    
      $urlsData = [];
      if(count($crawlerData) > 0 && $crawlerData[0][0] === 'links') {
        foreach($crawlerData as $key => $data) {
          if($key >= 1) {
            $urlsData[] = $data[0];
          }
        }
        return $this->singleJson($this->addUrls(new Request($urlsData)));
      } else {
        return $this->badRequestWithReason('Please try again. casv does not have specific format.');
      }
  }

  /**
   * @param Request $request
   * @return array
   */

  public function addUrlsForContactFormSearch(Request $request)
  {
    $urls = $request->all();
    $alreadySavedUrls = $urlsToSave = $saveUrlData = [];

    $existingUrls = CrawlerData::whereIn('link', $urls)->distinct('link')->pluck('link')->toArray(); 
    $batchId = $this->crawlerService->getBatchId(CrawlerTypes::CONTACT_FORM_CRAWLER) ?? 0;
    $batchId = $batchId + 1;
    if(isset($existingUrls) && count($existingUrls) > 0) {
      CrawlerData::whereIn('link', $urls)->update([
        'in_queue' => 1,
        'is_crawling' => 0,
        'is_invalid_url' => 0,
        'type' => CrawlerTypes::CONTACT_FORM_CRAWLER,
        'crawler_process_id' => null,
        'user_id' => null,
        'batch_id' =>$batchId,
      ]);
    }
    
    $urlsToSave = array_diff($urls, $existingUrls);

    foreach ($urlsToSave as $url) {
      $saveUrlData[] =  [
        'link' => $url,
        'in_queue' => 1,
        'type' => CrawlerTypes::CONTACT_FORM_CRAWLER,
        'batch_id' =>$batchId,
      ];
    }

    CrawlerData::insert($saveUrlData);
    
    return $urls;
  }

  public function start()
  {

    $result = $this->crawlerBackgroundProcessService->startCrawler();
    if ($result['success']) {
      return $this->singleJson($result);
    } else {
      return $this->accessDeniedWithReason($result['message']);
    }
  }

  public function status()
  {
    $result = $this->crawlerBackgroundProcessService->statusCrawler();
    return $this->singleJson($result);
  }

  public function stop()
  {
    $result = $this->crawlerBackgroundProcessService->stopCrawler();
    if ($result['success']) {
      return $this->singleJson($result);
    } else {
      return $this->notFoundWithReason('Prozess läuft nicht');
    }
  }

  public function startContactFormCrawler()
  {
    $result = $this->crawlerBackgroundProcessService->startContactFormCrawler();
    if ($result['success']) {
      return $this->singleJson($result);
    } else {
      return $this->accessDeniedWithReason($result['message']);
    }
  }

  public function stopContactFormCrawler()
  {
    $result = $this->crawlerBackgroundProcessService->stopContactFormCrawler();
    if ($result['success']) {
      return $this->singleJson($result);
    } else {
      return $this->notFoundWithReason('Prozess läuft nicht');
    }
  }

  public function statusContactFormCrawler()
  {
    $result = $this->crawlerBackgroundProcessService->statusContactFormCrawler();
    return $this->singleJson($result);
  }

  public function startDomainCrawler(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'user_id' => 'required',
      'links' => 'required'
    ]);

    if ($validator->fails()) {
      return $this->badRequestWithReason('Request parameters is missing');
    }

    $userId = $request->get('user_id');
    $links = $request->get('links');

    $crawlerProcessId = $this->crawlerService->addUrls($links, $userId);
    $result = $this->crawlerBackgroundProcessService->startDomainCrawler($userId, $crawlerProcessId);

    if ($result['success']) {
      return $this->singleJson($result);
    } else {
      return $this->accessDeniedWithReason($result['message']);
    }
  }

  public function domainCrawlerStatus($userId, $pid)
  {
    $result = $this->crawlerBackgroundProcessService->domainCrawlerStatus($pid, $userId);
    return $this->singleJson($result);
  }

  public function getDataWithCellNo()
  {
    $results = $this->crawlerService->getDataWithCellNo();

    if ($results) {
      return $results;
    } else if (emptyArray($results)) {
      return $this->notFound();
    }

    return $this->serverErrorQuick('An error occured while reading form results.');
  }

  public function checkCellNo(Request $request, $id)
  {

    $validator = Validator::make($request->all(), [
      'legal_cell_number_checked' => 'required|boolean'
    ]);

    $results = $this->crawlerService->checkCellNo($request->all(), $id);

    if ($results) {
      return $results;
    } else if (emptyArray($results)) {
      return $this->notFound();
    }

    return $this->serverErrorQuick('An error occured while updating results.');
  }

  /**
   * @return array
   */
  public function getAllBatchId() {
   return $this->crawlerService->getAllBatchId(CrawlerTypes::DASHBOARD_CRAWLER);
  }

  public function getAllBatchIdOfContactFormCrawler() {
    return $this->crawlerService->getAllBatchId(CrawlerTypes::CONTACT_FORM_CRAWLER);
  }
}
