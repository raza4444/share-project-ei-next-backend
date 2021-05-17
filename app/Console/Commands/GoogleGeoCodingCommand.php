<?php

namespace App\Console\Commands;

use App\Services\Branches\GoogleGeoCodingService;
use Illuminate\Console\Command;

class GoogleGeoCodingCommand extends Command
{
  protected $signature = 'application:update-location-coordinates {limit=1 : The maximum amount of customers to update}';

  public function handle(
    GoogleGeoCodingService $googleGeoCodingService
  ) {
    $limit = $this->argument('limit');
    $googleGeoCodingService->initializeCoordinatesForCustomers($limit);
  }
}
