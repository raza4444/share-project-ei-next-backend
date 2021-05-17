<?php

namespace App\Services\Branches;

use GuzzleHttp\Client;

class CompanyRegisterService
{
  private $client;

  public function __construct()
  {
    $this->client = new Client(
      [
        'base_uri' => env('NAREV_HOST'),
        'headers' => [
          'x-schluessel' => env('NAREV_HOST_TOKEN')
        ],
      ]
    );
  }

  /**
   * Returns first company which contains the search string
   * 
   * @param $searchStr the string to be searched for
   * 
   * @return array the array of matched companies
   */
  public function find($searchStr)
  {
    if (isset($searchStr)) {
      $res = $this->client->request('GET', '/rest/v2/unternehmen?search=' . urlencode($searchStr) . '&top=1');
      return $res ? $res->getBody() : [];
    }
  }

  /**
   * Creates a new company in the company-register
   * 
   * @param $loc the company which is about to get created
   * 
   * @return object the created company
   */
  public function create($loc)
  {
    if (isset($loc)) {
      $res = $this->client->request('POST', '/rest/v1/unternehmen', [
        'body' => json_encode($loc),
        'headers'  => ['content-type' => 'application/json']
      ]);
      return $res;
    }
  }

  /**
   * Adds actions to a given company in the company-register
   * 
   * @param $id the id of the company in the company-register
   * @param $actions the array of actions to add
   * 
   * @return object the updated company
   */
  public function addActions($id, $actions)
  {
    if (isset($id) && isset($actions)) {
      $res = $this->client->request('POST', '/rest/v1/unternehmen/'. $id .'/aktionen', [
        'body' => json_encode($actions),
        'headers'  => ['content-type' => 'application/json']
      ]);
      return $res;
      // return [
      //     'body' => json_encode($actions),
      //     'headers'  => ['content-type' => 'application/json']
      // ];
    }
  }
}
