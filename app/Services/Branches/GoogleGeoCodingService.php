<?php

namespace App\Services\Branches;

use App\Logging\LoggingClip;
use App\Repositories\Branches\LocationRepository;
use App\Utils\StringUtils;
use Exception;

class GoogleGeoCodingService
{

  private $clip;
  private $locationRepository;

  public function __construct(LocationRepository $locationRepository)
  {
    $this->clip = new LoggingClip('google-geo-coding', StringUtils::createGUID());
    $this->locationRepository = $locationRepository;
  }

  /**
   * Sets the coordinates of all customers without location coordinates
   * 
   * @param integer $limit the maximum amount of customer to update
   * 
   * @return void
   */
  public function initializeCoordinatesForCustomers($limit)
  {
    $customers = $this->locationRepository->findCustomersWithoutLocationCoordinates($limit);
    foreach ($customers as $customer) {
      $this->setCoordinatesForCustomer($customer);
    }
  }

  /**
   * Updates the coordinates of all customers
   * 
   * @return void
   */
  public function updateCoordinatesForAllCustomers()
  {
    $customers = $this->locationRepository->findCustomers();
    foreach ($customers as $customer) {
      $this->setCoordinatesForCustomer($customer);
    }
  }

  /**
   * Sets the location coordiantes for a given customer id
   * 
   * @param integer $id the customer id
   * 
   * @return void
   */
  public function setCoordinatesForCustomer($customer)
  {
    if (isset($customer) && isset($customer['zip']) && isset($customer['street'])) {
      $this->clip->info('Updating coordinates for customer ' . $customer['id'] . '.');
      $coordinates = $this->getCoordinatesForAddress($customer['street'], $customer['zip'], $customer['city']);
      if ($coordinates && sizeof($coordinates) === 2) {
        $customer['location_lng'] = $coordinates[0];
        $customer['location_lat'] = $coordinates[1];
        $this->locationRepository->update($customer);
        $this->clip->info('Coordinates of customer ' . $customer['id'] . ' updated.');
      } else {
        $this->clip->error('Received coordinates invalid.');
      }
    } else {
      $this->clip->error('Customer with ID ' . $customer->id . ' not found, zip code or street not set.');
    }
  }

  /**
   * Returns coordinates for a given address
   * 
   * @param String $address the full address
   * @return String the coordinates 
   */
  private function getCoordinatesForAddress($street, $zip, $city)
  {
    $this->clip->info('Getting coordinates for address "' . $street . ' ' . $zip . ' ' . $city . '"');

    // Formatting address
    $street = str_replace(' ', '%20', $street);
    $city = str_replace(' ', '%20', $city);

    try {
      $crl = curl_init(env('GOOGLE_GEOCODING_API') . '?address=' . $street . '%20' . $zip . '%20' . $city . '%20Germany&key=' . env('GOOGLE_GEOCODING_API_KEY'));
      curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
      $resp = curl_exec($crl);
      $json = json_decode($resp, true);
      curl_close($crl);

      // $this->clip->info($resp);

      if ($json && array_key_exists('results', $json) && sizeof($json['results']) === 1) {
        if (array_key_exists('types', $json['results'][0]) && ($json['results'][0]['types'][0] === 'street_address' || $json['results'][0]['types'][0] === 'premise')) {
          if (array_key_exists('geometry', $json['results'][0])) {
            $lat = $json['results'][0]['geometry']['location']['lat'];
            $lng = $json['results'][0]['geometry']['location']['lng'];
            $this->clip->info('Lat: ' . $lat);
            $this->clip->info('Lng: ' . $lng);
            return [$lng, $lat];
          } else {
            $this->clip->error('"geometry" key of response missing.');
          }
        } else {
          $this->clip->error('"types" key of response missing.');
        }
      } else {
        $this->clip->error('Result of response missing or multiple location results.');
      }
    } catch (Exception $e) {
      $this->clip->error('An error occured:');
      $this->clip->error($e->getMessage());
    }

  }
}
