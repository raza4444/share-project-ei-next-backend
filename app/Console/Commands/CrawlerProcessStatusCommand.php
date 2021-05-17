<?php

namespace App\Console\Commands;

use App\Repositories\Crawler\CrawlerRepository;
use App\Entities\Crawler\CrawlerTypes;
use Illuminate\Console\Command;

class CrawlerProcessStatusCommand extends Command
{
  protected $name = 'application:crawler-process-status';

  public function handle(
    CrawlerRepository $crawlerRepository
  ) {
    $dashboardCrawler =  $crawlerRepository->findRunningProcess(CrawlerTypes::DASHBOARD_CRAWLER);
    if (isset($dashboardCrawler) && isset($dashboardCrawler->id)) {
      $this->info("Process is running of " . CrawlerTypes::DASHBOARD_CRAWLER . ",   ");
    } else {
      $this->info("No running process found of " . CrawlerTypes::DASHBOARD_CRAWLER . ' crawler,  ');
    }

    $contactFormCrawler = $crawlerRepository->findRunningProcess(CrawlerTypes::CONTACT_FORM_CRAWLER);
    if (isset($contactFormCrawler) && isset($contactFormCrawler->id)) {
      $this->info("Process is running of " . CrawlerTypes::CONTACT_FORM_CRAWLER . ",   ");
    } else {

      $this->info("No running process found of " . CrawlerTypes::CONTACT_FORM_CRAWLER . ' crawler,  ');
    }
  }
}
