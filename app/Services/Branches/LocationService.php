<?php

namespace App\Services\Branches;

use App\Repositories\Branches\LocationRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class LocationService
{
  private $locationRepository;

  public function __construct(LocationRepository $locationRepository)
  {
    $this->locationRepository = $locationRepository;
  }

  public function findAllWithoutOpenDuties()
  {
    return DB::select('
      SELECT cl.id, cl.title from campaign_locations as cl
      WHERE NOT EXISTS(
        SELECT 1 FROM duty_rows AS dr
        WHERE dr.locationId = cl.id
        AND dr.done = 0)
      AND EXISTS(
        SELECT 1 FROM appointments AS a
        WHERE a.locationId = cl.id)
      GROUP BY cl.id, cl.title
      ORDER BY cl.id DESC LIMIT 10000
    ');
  }
  
  public function findByEmail($email)
  {
    return $this->locationRepository->findByEmail($email);
  }

  /**
   * Connects to a FTP server with the given credentials
   * and returns if the connection was successful
   * 
   * @param String $ftpHost
   * @param String $ftpUser
   * @param String $ftpPassword
   * @return Boolean
   */
  public function testFtpConnection($ftpHost, $ftpUser, $ftpPassword)
  {
    try {
      $con = ftp_connect($ftpHost);
      if (!$con) {
        return false;
      } else {
        if (ftp_login($con, $ftpUser, $ftpPassword)) {
          return true;
        }
      }
    } catch (Exception $e) {
      // Connection not successful or other error
      return false;
    }
  }

  /**
   * Returns coordinates for all customers
   * 
   * @return array the coordinates array
   */
  public function findCoordinatesForCustomers()
  {
    return $this->locationRepository->findCoordinatesForCustomers();
  }
}
