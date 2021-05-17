<?php

namespace App\Services\Crawler;

use App\Logging\LoggingClip;
use App\Repositories\Branches\LocationRepository;
use App\Repositories\Crawler\CrawlerMatchRepository;
use App\Repositories\Crawler\CrawlerResultRepository;
use App\Services\Branches\CompanyRegisterService;
use App\Utils\StringUtils;
use DateTime;

class CrawlerMatchService
{

  /**
   * @var CrawlerMatchRepository
   */
  private $crawlerMatchRepository;

  /**
   * @var CrawlerResultRepository
   */
  private $crawlerResultRepository;

  /**
   * @var LocationRepository
   */
  private $locationRepository;

  /**
   * @var CompanyRegisterService
   */
  private $companyRegisterService;

  private const SUCCESSFULLY_REPORTED = 1;
  private const DOMAIN_NOT_FOUND = 4;
  private const MAX_RETURN_RESULTS = 50000;

  /**
   * @param CrawlerMatchRepository $crawlerMatchRepository
   * @param CrawlerResultRepository $crawlerMatchRepository
   * @param LocationRepository $locationRepository
   * @param CompanyRegisterService $companyRegisterService
   */
  public function __construct(
    CrawlerMatchRepository $crawlerMatchRepository,
    CrawlerResultRepository $crawlerResultRepository,
    LocationRepository $locationRepository,
    CompanyRegisterService $companyRegisterService
  ) {
    $this->crawlerMatchRepository = $crawlerMatchRepository;
    $this->crawlerResultRepository = $crawlerResultRepository;
    $this->locationRepository = $locationRepository;
    $this->companyRegisterService = $companyRegisterService;
  }

  public function getKeywords()
  {
    return $this->crawlerMatchRepository->getKeywords();
  }

  /**
   * @param array $keywordObj the keyword to be added
   * @return object|null
   */
  public function addKeyword($keywordObj)
  {
    return $this->crawlerMatchRepository->addKeyword($keywordObj);
  }

  /**
   * @param int $id the id of the keyword to be updated
   * @param array $updatedKeyword the keyword to be updated
   * @return object|null
   */
  public function updateKeyword($id, $updatedKeyword)
  {
    return $this->crawlerMatchRepository->updateKeyword($id, $updatedKeyword);
  }

  /**
   * @param int $id the id of the keyword to be deleted
   */
  public function deleteKeyword($id)
  {
    $this->crawlerMatchRepository->deleteKeyword($id);
  }

  public function reportResults()
  {
    $clip = new LoggingClip('crawler-report', StringUtils::createGUID());

    $urls = $this->crawlerMatchRepository->getResultsWithLockCriteriaOrMatchedKeywords();

    foreach ($urls as $url) {

      if (isset($url->link)) {

        $parsedUrl = str_replace('www.', '', parse_url('http://' . str_replace(array('https://', 'http://'), '', $url->link), PHP_URL_HOST));

        $clip->info('Searching company register for domain "' . $parsedUrl . '"');

        $foundCompany = $this->companyRegisterService->find($parsedUrl);
        $parsedCompany = json_decode($foundCompany);

        if (isset($parsedCompany) && sizeof($parsedCompany) > 0) {

          // Found, add action
          $clip->info('Company with domain "' . $parsedUrl . '" was found (' . $parsedCompany[0]->id . ').');

          $action = 'gesperrt,kalt,kontakt,wegen';

          // Main lock criteria main page
          if ($url->has_search) {
            $action .= ',suche';
          }

          if ($url->has_shop) {
            $action .= ',shop';
          }

          if ($url->has_newsletter) {
            $action .= ',newsletter';
          }

          // Main lock criteria sub pages
          foreach ($url->subUrls as $subUrl) {

            if ($subUrl->has_search) {
              if (!str_contains($action, 'suche')) {
                $action .= ',suche';
              }
            }

            if ($subUrl->has_shop) {
              if (!str_contains($action, 'shop')) {
                $action .= ',shop';
              }
            }

            if ($subUrl->has_newsletter) {
              if (!str_contains($action, 'newsletter')) {
                $action .= ',newsletter';
              }
            }
          }

          if ($url->keywordresults) {
            foreach ($url->keywordresults as $result) {
              if ($result->keyword->report_result) {
                if (!str_contains($action, $result->keyword->keyword)) {
                  $action .= ',' . $result->keyword->keyword;
                }
              }
            }
          }

          $action .= ',durch,crawler';

          $clip->info('Add action(s) to company (' . $parsedCompany[0]->id . '):');
          $clip->info(json_encode($action));

          // Send request
          $res = $this->companyRegisterService->addActions($parsedCompany[0]->id, [$action]);

          try {
            $clip->info('Response code: ' . $res->getStatusCode());
            $clip->info('Result report for company (' . $parsedCompany[0]->id . ') was successful.');
          } catch (\Throwable $th) {
            $clip->error('Error: ' . $th->getMessage());
          }

          $url->reported_result = json_encode($action);
          $url->report_status = self::SUCCESSFULLY_REPORTED;
        } else {
          $clip->error('Company with domain "' . $parsedUrl . '" was not found.');
          $url->report_status = self::DOMAIN_NOT_FOUND;
        }
      } else {
        $clip->error('Link for crawler data entry ' . $url->id . ' is empty.');
      }

      $url->checked_results_at = new DateTime();
      $this->crawlerResultRepository->updateResult($url, null);
    }
  }

  /**
   * @param string $type the crawler type whose results are requested
   */
  public function getResults($type, $batchId)
  {

    if ($type === 'url') {

      $urls = $this->crawlerResultRepository->getUrlCrawlerResultsForResultsList(self::MAX_RETURN_RESULTS, $batchId);
      foreach ($urls as $url) {

        $parsedUrl = str_replace('www.', '', parse_url('http://' . str_replace(array('https://', 'http://'), '', $url->link), PHP_URL_HOST));

        $campaignLocation = $this->locationRepository->findForCrawlerResultReport($parsedUrl);

        if ($campaignLocation) {
          $url->customer_state = $campaignLocation['customerstate'];
        }
      }

      return $urls;
    } else if ($type === 'contact-form') {
      return $this->crawlerResultRepository->getContactFormCrawlerResultsForResultsList(self::MAX_RETURN_RESULTS, $batchId);
    }
  }

  /**
   * @param string $type the crawler type whose results are requested
   * @param string $domain the domain whose results are requested
   */
  public function getResultsForDomain($type, $domain)
  {
    return $this->crawlerResultRepository->getCrawlerResultsForDomain($type, $domain);
  }
}
