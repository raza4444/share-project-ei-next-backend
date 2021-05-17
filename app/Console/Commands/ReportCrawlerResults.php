<?php

namespace App\Console\Commands;

use App\Http\Controllers\Intern\Crawler\MatchController;
use App\Services\Crawler\CrawlerMatchService;
use Illuminate\Console\Command;

class ReportCrawlerResults extends Command
{
  protected $name = 'application:report-crawler-results';

  public function handle(
    CrawlerMatchService $crawlerMatchService
  ) {
    $crawlerMatchService->reportResults();
  }
}
