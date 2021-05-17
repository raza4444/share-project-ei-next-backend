<?php

/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Branches;

use App\Entities\Branches\Location;
use App\Http\Controllers\AbstractInternController;
use App\Logging\LoggingClip;
use App\Repositories\Branches\BasicLocationFilter;
use App\Repositories\Branches\ForEventCreationFilter;
use App\Repositories\Branches\LocationRepository;
use App\Services\Branches\GoogleGeoCodingService;
use App\Services\Branches\LocationDeactivationService;
use App\Services\Branches\LocationService;
use App\Services\Branches\LocationStatisticsService;
use App\Services\Customers\CustomerPasswordService;
use App\Utils\StringUtils;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends AbstractInternController
{

  private $locationRepository;
  private $locationDeactivationService;
  private $locationStatisticsService;
  private $locationService;
  private $customerPasswordService;
  private $googleGeoCodingService;
  private $clip;

  public function __construct(
    LocationRepository $locationRepository,
    LocationStatisticsService $locationStatisticsService,
    LocationDeactivationService $locationDeactivationService,
    CustomerPasswordService $customerPasswordService,
    GoogleGeoCodingService $googleGeoCodingService,
    LocationService $locationService
  ) {
    $this->locationRepository = $locationRepository;
    $this->locationDeactivationService = $locationDeactivationService;
    $this->locationStatisticsService = $locationStatisticsService;
    $this->customerPasswordService = $customerPasswordService;
    $this->locationService = $locationService;
    $this->googleGeoCodingService = $googleGeoCodingService;
    $this->clip = new LoggingClip('google-geo-coding', StringUtils::createGUID());
  }

  /**
   * Creates a new location referencing to the narev locations table (narev_id) after looking for
   * the location's phone #
   * 
   * @param Request $request
   * @return JsonResponse
   */
  public function createFromLocationV2Event(Request $request)
  {
    $req = $request->all();
    $eventParams = $req['event']['ereignis'];
    $locParams = $req['event']['unternehmen'];
    $result = $req['result'] && $req['result'] !== 0 ? $req['result'] : null;

    $newLoc = new Location([
      'company_register_id' => $locParams['id'],
      'company_register_event_id' => $eventParams['id'],
      'result' => $result,
      'title' => $locParams['name'],
      'street' => $locParams['strasse'],
      'zip' => $locParams['plz'],
      'country' => $locParams['land'] ? $locParams['land'] : 'Deutschland',
      'phoneNumber' => $locParams['telefon'],
      'email' => $locParams['email'],
      'homepage' => $locParams['domain'],
      'city' => $locParams['ort'],
      'werbeaktion' => $locParams['werbeaktion'],
      'canLogin' => 0, // to avoid default true
      'said_whatsapp' => 0 // to avoid null values
    ]);

    if ($newLoc->saveOrFail()) {
      $createdLoc = Location::where('id', $newLoc->id)->first();
      return response()->json($createdLoc, 201);
    }
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function createManually(Request $request)
  {
    $values = $request->all();
    $loc = new Location();
    $setCoordinates = false;
    foreach ($values as $k => $v) {
      if ($k != 'id' && $k != 'notes_category') {
        $loc->$k = $v;
      }
      // Set coordinates of location if new entry got marked as customer
      if ($k === 'customerstate' && $v == 10) {
        $setCoordinates = true;
      }
    }

    $autoGenerateAccessData = StringUtils::isEmpty($loc->username);

    if (isset($values['phoneNumber']) && isset($values['mobilePhoneNumber'])) {
      $phoneNumber = $values['phoneNumber'];
      $mobilePhoneNumber = $values['mobilePhoneNumber'];

      try {
        $this->locationDeactivationService->deactivateByPhoneNumber($phoneNumber);
        $this->locationDeactivationService->deactivateByPhoneNumber($mobilePhoneNumber);
      } catch (\Throwable $t) {
        Log::error('error in creation of location while deactivating phone numbers');
      }
    }

    $loc->id = $this->locationRepository->createManuallyEntered($loc);

    if ($autoGenerateAccessData) {
      $this->customerPasswordService->writeDefaultAccessDataAndEnableAccount($loc->id);
    }

    if ($setCoordinates) {
      $this->clip->info('New location ' . $loc->id . ' created that has been marked as "customer". Setting coordinates...');
      $this->googleGeoCodingService->setCoordinatesForCustomer($loc);
    }

    return $this->singleJson($loc);
  }

  public function countByFederalState($stateId)
  {
    $count = $this->locationRepository->countByFederalStateId($stateId);
    return $this->singleJson(['count' => $count], 200);
  }

  public function countByCategory($id)
  {
    $count = $this->locationRepository->countByCategoryId($id);
    return $this->singleJson(['count' => $count], 200);
  }

  public function countByCategoryAndNoEventsSinceMonths($id, $months)
  {
    $count = $this->locationRepository->countByCategoryIdAndNoEventsSinceMonths($id, $months);
    return $this->singleJson(['count' => $count], 200);
  }

  public function find(Request $request)
  {
    $filter = $request->get('filter');
    if ($filter == 'forCreateEvents') {
      return $this->json(200, $this->findForEventCreation($request));
    } else if ($filter == 'basic') {
      return $this->json(200, $this->findGeneric($request));
    } else {
      return $this->json(200, []);
    }
  }

  public function deactivateById($id)
  {
    $this->locationDeactivationService->deactivateLocationById($id);
    return $this->noContent();
  }

  public function updateById($id, Request $request)
  {
    $values = $request->all();
    $loc = $this->locationRepository->byIdActive($id);
    if ($loc == null) {
      return $this->notFound();
    }

    $updatedCoordinates = false;

    foreach ($values as $k => $v) {

      if ($k === 'customerstate' && $v == 10) {
        if ($loc->$k != 10) { // Set coordinates of location if new customer
          $this->clip->info($loc->id . ' got marked as "customer". Updating coordinates...');
          $updatedCoordinates = true;
        } else if ($loc->location_lat == '' || $loc->location_lng == '') { // Customer updated, but coordinates incomplete
          $this->clip->info('Location coordinates for ' . $loc->id . ' incomplete, updating...');
          $updatedCoordinates = true;
        }
      }

      // Address changes
      if ($k === 'street' && $loc->street !== $values['street']) {
        $this->clip->info('Address (street) of ' . $loc->id . ' has changed.');
        $updatedCoordinates = true;
      }
      if ($k === 'zip' && $loc->zip !== $values['zip']) {
        $this->clip->info('Address (zip) of ' . $loc->id . ' has changed.');
        $updatedCoordinates = true;
      }
      if ($k === 'city' && $loc->city !== $values['city']) {
        $this->clip->info('Address (city) of ' . $loc->id . ' has changed.');
        $updatedCoordinates = true;
      }

      if ($k != 'id' && $k != 'numbers' && $k != 'emails' && $k != 'federal_state' && $k != 'location_category' && $k != 'info_data' && $k != 'location_lat' && $k != 'location_lng') {
        if (!is_array($v)) {
          $loc->$k = $v;
        }
      }
      if (substr($k, 0, 7) === 'agentId' && $v === '') {
        $loc->$k = null;
      }
    }

    $loc->save();

    if ($updatedCoordinates) {
      $this->googleGeoCodingService->setCoordinatesForCustomer($loc);
    }

    return $this->singleJson($loc->toArray());
  }

  public function findById($id)
  {
    $loc = $this->locationRepository->findByIdComplete($id);
    if ($loc == null) {
      return $this->notFound();
    }
    return $this->singleJson($loc->toArray());
  }

  public function getGroupBySpecificColumn(Request $request, $columnName)
  {
    $availableTColumnForGroupBy = ['sub-category', 'werbeaktion'];
    if (in_array($columnName, $availableTColumnForGroupBy)) {
      $columnName =  str_replace("-", "_", $columnName);
      $locations =  $this->locationRepository->getGroupBySpecificColumn($request, $columnName);
      if (!is_array($locations) && $locations == null) {
        return $this->notFound();
      }
      return $this->singleJson($locations);
    }
  }

  public function locationsPerCategoryAndState()
  {
    return $this->json(200, $this->locationStatisticsService->locationsPerCategoryAndState());
  }

  private function findGeneric(Request $request)
  {
    $filter = new BasicLocationFilter();
    $filter->categoryId = $request->has('categoryId') ? $request->get('categoryId') : null;
    $filter->search = $request->has('search') ? $request->get('search') : null;
    $filter->loadAllOfCompany = $request->has('full') && $request->get('full') == '1';
    $filter->limit = $request->has('limit') ? $request->get('limit') * 1 : null;
    $filter->full = $request->has('full') ? true : false;
    $filter->type = $request->has('type') ? $request->get('type') : null;
    $filter->year = $request->has('year') ? $request->get('year') * 1 : null;
    $filter->cancellation = $request->has('cancellation') ? $request->get('cancellation') : false;
    $filter->revocation = $request->has('revocation') ? $request->get('revocation') : false;
    $filter->year = $request->has('year') ? $request->get('year') * 1 : null;
    $filter->title = $request->has('title') ? $request->get('title') : null;
    $filter->phoneNumber = $request->has('phoneNumber') ? $request->get('phoneNumber') : null;
    $filter->mobilePhoneNumber = $request->has('mobilePhoneNumber') ? $request->get('mobilePhoneNumber') : null;
    $filter->email = $request->has('email') ? $request->get('email') : null;
    $filter->fax = $request->has('fax') ? $request->get('fax') : null;
    $filter->domain = $request->has('domain') ? $request->get('domain') : null;
    $filter->companyRegisterId = $request->has('companyRegisterId') ? $request->get('companyRegisterId') : null;

    return $this->locationRepository->findGeneric($filter);
  }

  private function findForEventCreation(Request $request)
  {
    
    $filter = new ForEventCreationFilter();
    $filter->categoryId = $request->get('categoryId');
    $filter->noEventsSinceMonths = $request->get('noEventSince');
    $filter->stateIds = explode(',', $request->get('state-ids'));

    $locations = $this->locationRepository->findForEventCreation($filter, ['id']);
    return $locations;
  }

  public function findAllWithoutOpenDuties()
  {
    return $this->singleJson($this->locationService->findAllWithoutOpenDuties());
  }

  /**
   * Connects to a FTP server with the given credentials
   * and returns if the connection was successful
   * 
   * @param Request $request
   * @return Boolean
   */
  public function testFtpConnection(Request $request)
  {
    $vData = $this->validate($request, [
      'ftp_host' => 'required',
      'ftp_user' => 'required',
      'ftp_password' => 'required'
    ]);

    $res = $this->locationService->testFtpConnection($vData['ftp_host'], $vData['ftp_user'], $vData['ftp_password']);

    return $this->singleJson($res);
  }

  /**
   * Returns coordinates for all customers
   * 
   * @return array the coordinates array
   */
  public function findCoordinatesForCustomers()
  {
    return $this->locationService->findCoordinatesForCustomers();
  }
}
