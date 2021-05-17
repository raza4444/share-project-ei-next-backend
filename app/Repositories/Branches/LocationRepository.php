<?php

/**
 * by stephan scheide
 */

namespace App\Repositories\Branches;

use App\Entities\Branches\Location;
use App\Entities\Branches\LocationCategory;
use App\Entities\Branches\LocationEmail;
use App\Entities\Branches\LocationPhoneNumber;
use App\Entities\States\FederalState;
use App\Repositories\AbstractRepository;
use App\Utils\PhoneNumberHelper\PhoneNumberHelperImpl;
use App\Utils\Str;
use App\Utils\StringUtils;
use Illuminate\Support\Facades\DB;
use voku\helper\UTF8;
use App\Entities\Core\PermissionType;

class LocationRepository extends AbstractRepository
{

  private $phoneNumberHelper;

  public function __construct(PhoneNumberHelperImpl $phoneNumberHelper)
  {
    parent::__construct(Location::class);
    $this->phoneNumberHelper = $phoneNumberHelper;
  }

  /**
   * Returns customer record for a given id
   * 
   * @param number $id the id
   * 
   * @return array the customers
   */
  public function find($id)
  {
    return $this->query()->find($id);
  }

  /**
   * Returns customer record for a given email
   * 
   * @param email $email the email
   * 
   * @return array the customer
   */
  public function findByEmail($email)
  {
    $loc = Location::query()
      ->where('email', '=', $email)
      ->first();
    return $loc;
  }

  public function update($location)
  {
    $location->save();
  }

  /**
   * Returns all customers
   * 
   * @return array the customers
   */
  public function findCustomers()
  {
    return $this->query()
      ->where('customerstate', 10)
      ->where('ftp_credentials_checked', true)
      ->get();
  }

  /**
   * Returns all customers without location coordinates
   * 
   * @param integer $limit the maximum amount of customers to return
   * 
   * @return array the customers without coordinates
   */
  public function findCustomersWithoutLocationCoordinates($limit)
  {
    return $this->query()
      ->where('ftp_credentials_checked', true)
      ->where('customerstate', 10)
      ->where(function($q) {
        $q->whereNull('location_lat')
        ->orWhereNull('location_lng');
      })
      ->limit($limit)
      ->get();
  }

  /**
   * Returns coordinates for all customers
   * 
   * @return array the coordinates array
   */
  public function findCoordinatesForCustomers()
  {
    return $this->query()
      ->where('customerstate', 10)
      ->whereNotNull('location_lat')
      ->whereNotNull('location_lng')
      ->select([
        'id',
        'title',
        'street',
        'zip',
        'city',
        'location_lat',
        'location_lng'
      ])
      ->get();
  }

  /**
   * returns all companies with data filled for ssl job creation
   * @return Location[]
   */
  public function findForSslJobCreation()
  {
    return $this->query()
      ->whereNotNull('domain')
      ->whereNotNull('ftphost')
      ->whereNotNull('ftpusername')
      ->whereNotNull('ftppassword')
      ->whereNotNull('ftpdirectoryhtml')
      ->whereRaw('(length(domain)>2)')
      ->whereRaw('(length(ftphost)>2)')
      ->whereRaw('(length(ftpusername)>2)')
      ->whereRaw('(length(ftppassword)>2)')
      ->whereRaw('(length(ftpdirectoryhtml)>2)')
      ->whereRaw('( (agentId3 is null) or (agentId3<>137))')
      ->whereRaw('(ssl_active=1)')
      ->get();
  }

  public function existsByPhoneNumber($phoneNumber)
  {
    $phoneNumber = $this->phoneNumberHelper->correctPhoneNumber($phoneNumber);
    $loc = Location::query()
      ->where('phoneNumber', '=', $phoneNumber)
      ->first();
    return $loc != null;
  }

  public function importSchool($title, $city, FederalState $state, $country, $phoneNumber, $fax, $email, $categoryName, $zip, $street = null)
  {
    if (!empty($city)) $city = Str::ucfirst(UTF8::fix_utf8($city));
    if (!empty($street)) $street = Str::ucfirst(UTF8::fix_utf8($street));
    if (!empty($title)) $title = Str::ucfirst(UTF8::fix_utf8($title));
    if (!empty($fax)) $fax = UTF8::fix_utf8($fax);
    if (!empty($phoneNumber)) $phoneNumber = UTF8::fix_utf8($phoneNumber);
    if (!empty($country)) $country = Str::ucfirst(UTF8::fix_utf8($country));
    if (!empty($categoryName)) $categoryName = explode(",", UTF8::fix_utf8($categoryName))[0];
    if (!empty($email)) $email = Str::lower(UTF8::fix_utf8($email));
    if (!empty($zip)) $zip = UTF8::trim(Str::lower(UTF8::fix_utf8($zip)));

    if (!empty($phoneNumber)) $phoneNumber = $this->phoneNumberHelper->correctPhoneNumber($phoneNumber);
    if (!empty($fax)) $fax = $this->phoneNumberHelper->correctPhoneNumber($fax);

    if (!empty($categoryName)) {
      $category = $this->getOrCreateCategory($categoryName);
    }

    $school = new Location();
    $school->title = $title;
    $school->city = $city;
    $school->country = $country;
    $school->zip = $zip;
    $school->phoneNumber = $phoneNumber;
    $school->fax = $fax;
    $school->email = $email;
    $school->street = $street;

    if (!empty($category)) {
      $school->locationCategory()->associate($category);
    }

    $school->federalState()->associate($state);

    $school->save();
    return $school;
  }

  /**
   * @param $title
   * @return LocationCategory|null
   */
  public function getOrCreateCategory($title)
  {
    $title = trim($title);
    if (empty($title)) {
      return null;
    }

    /** @var LocationCategory $exists */
    $exists = LocationCategory::query()
      ->where("title", "=", $title)
      ->first();

    if (!empty($exists)) {
      return $exists;
    }

    $entry = new LocationCategory();
    $entry->title = $title;
    $entry->save();

    return $entry;
  }

  /**
   * Liefert die Anzahl der Unternehmen im angegebenen Bundesland
   * @param $stateId
   * @return int
   */
  public function countByFederalStateId($stateId)
  {
    return $this->query()
      ->where('bundeslandid', '=', $stateId)
      ->whereNull('deleted_at')
      ->count();
  }

  public function countByCategoryId($id)
  {
    return $this->query()
      ->where('locationcategoryid', '=', $id)
      ->whereNull('deleted_at')
      ->count();
  }

  public function countByCategoryIdAndNoEventsSinceMonths($id, $months)
  {
    $date = date('Y-m-d H:i:s', strtotime("-$months months"));

    return $this->query()
      ->where('campaign_locations.locationCategoryId', '=', $id)
      ->whereNull('campaign_locations.deleted_at')
      ->whereNotExists(function ($query) use ($date) {
        $query->select(DB::raw(1))
          ->from('campaign_location_events as e')
          ->whereRaw('(e.schoolid = campaign_locations.id)')
          ->whereNull('e.deleted_at')
          ->where('e.created_at', '>=', $date);
      })
      ->count();
  }

  public function findForEventCreation(ForEventCreationFilter $filter, $fields = null)
  {
    $months = $filter->noEventsSinceMonths;
    $date = date('Y-m-d H:i:s', strtotime("-$months months"));

    $whereRaw = '';
    if ($filter->stateIds != null && count($filter->stateIds) > 0) {
      foreach ($filter->stateIds as $sid) {
        $sid = $sid * 1;
        $whereRaw .= '(campaign_locations.bundeslandid=' . $sid . ') or ';
      }
      $whereRaw = '(' . substr($whereRaw, 0, -3) . ')';
    }

    $query = $this->query()
      ->where('campaign_locations.locationCategoryId', '=', $filter->categoryId)
      ->whereNull('campaign_locations.deleted_at')
      ->whereNotExists(function ($query) use ($date) {
        $query->select(DB::raw(1))
          ->from('campaign_location_events as e')
          ->whereRaw('(e.schoolid = campaign_locations.id)')
          ->whereNull('e.deleted_at')
          ->where('e.created_at', '>=', $date);
      });

    if ($fields != null) {
      $query->select($fields);
    }

    if (strlen($whereRaw) > 0) {
      $query->whereRaw($whereRaw);
    }

    return $query->get();
  }

  /**
   * @return Location[]
   */
  public function findSelfHosted()
  {
    $this->query()->where('agentId3', '=', 137)->get();
  }

  /**
   * @param $number
   * @return Location[]
   */
  public function findLocationsByPhoneNumber($number)
  {
    return $this->query()
      ->whereNull('deleted_at')
      ->where('phoneNumber', '=', $number)
      ->orWhere('mobilePhoneNumber', '=', $number)
      ->get();
  }

  public function findGeneric(BasicLocationFilter $filter)
  {
    $query = $this->query();
    $query->withTrashed();
    $comparisonType = 'LIKE';

    if ($filter->full) {
      $comparisonType = '=';
    }

    if ($filter->categoryId !== null) {
      $query->where('locationCategoryId', '=', $filter->categoryId);
    }

    if ($filter->title !== null) {
      $query->where('title', $comparisonType, (!$filter->full ? '%' : '') . $filter->title . (!$filter->full ? '%' : ''));
    }

    if ($filter->phoneNumber !== null) {
      $query->where('phoneNumber', $comparisonType, (!$filter->full ? '%' : '') . $filter->phoneNumber . (!$filter->full ? '%' : ''));
    }

    if ($filter->mobilePhoneNumber !== null) {
      $query->where('mobilePhoneNumber', $comparisonType, (!$filter->full ? '%' : '') . $filter->mobilePhoneNumber . (!$filter->full ? '%' : ''));
    }

    if ($filter->email !== null) {
      $query->where('email', $comparisonType, (!$filter->full ? '%' : '') . $filter->email . (!$filter->full ? '%' : ''));
    }

    if ($filter->fax !== null) {
      $query->where('fax', $comparisonType, (!$filter->full ? '%' : '') . $filter->fax . (!$filter->full ? '%' : ''));
    }

    if ($filter->domain !== null) {
      $query->where('domain', $comparisonType, (!$filter->full ? '%' : '') . $filter->domain . (!$filter->full ? '%' : ''));
    }

    if ($filter->companyRegisterId !== null) {
      $query->where('company_register_id', $filter->companyRegisterId);
    }

    if ($filter->search !== null) {
      $id = StringUtils::ensureInteger($filter->search, 0);
      $pattern = (!$filter->full ? '%' : '') . $filter->search . (!$filter->full ? '%' : '');
      $query->where('title', $comparisonType, $pattern);
      $query->orWhere('phoneNumber', $comparisonType, $pattern);
      $query->orWhere('company_register_id', $comparisonType, $pattern);
      if ($id > 0) $query->orWhere('id', '=', $id);
    }


    if ($filter->loadAllOfCompany) {
      $query->with('events');
      $query->with('events.tracks');
      $query->with('events.notes');
    }
    if ($filter->type == 'statistics') {
      if ($filter->year != null) {
        if ($filter->cancellation && !$filter->revocation) {
          $query->select(DB::raw("date_of_cancellation ,  sum(canceled) as canceled"));
        }
        if ($filter->revocation && !$filter->cancellation) {
          $query->select(DB::raw("date_of_cancellation , sum(revoked) as revoked"));
        }
        if ($filter->revocation && $filter->cancellation) {
          $query->select(DB::raw("date_of_cancellation ,  sum(canceled) as canceled , sum(revoked) as revoked"));
        }
        $query->having('date_of_cancellation', '>=', $filter->year . '-01-01 00:00:00');
        $query->having('date_of_cancellation', '<=', $filter->year . '-12-31 23:59:59');
        $query->groupBy('date_of_cancellation');
      }
    }
    if ($filter->limit !== null) {
      $query->limit($filter->limit);
    }


    return $query->get();
  }

  public function createManuallyEntered(Location $location)
  {
    $location->manuellErstellt = 1;
    $location->locationCategoryId = null;
    $location->country = 'Deutschland';
    $location->save();
    return $location->id;
  }

  public function addPhoneNumber(Location $location, $number)
  {
    $n = new LocationPhoneNumber();
    $n->phonenumber = $this->phoneNumberHelper->correctPhoneNumber($number);
    $n->locationid = $location->id;
    $n->save();
    return $n;
  }

  public function addEmail(Location $location, $email, $type = LocationEmail::TYPE_CONTACT)
  {
    $e = new LocationEmail();
    $e->email = $email;
    $e->locationid = $location->id;
    $e->typ = $type;
    $e->save();
    return $e;
  }

  /**
   * @param $id
   * @return Location|null
   */
  public function findByIdComplete($id)
  {
    return $this->query()
      ->where('id', '=', $id)
      ->with('emails')
      ->with('numbers')
      ->with('events')
      ->with('federalState')
      ->with('locationCategory')
      ->with('infoData')
      ->withTrashed()
      ->first();
  }

  /**
   * @param $columnName
   * @return Location|null
   */
  public function getGroupBySpecificColumn($request, $columnName)
  {
    $from = null;
    $to = null;

    if ($request->has('from')) {
      $from = $request->from;
    }

    if ($request->has('to')) {
      $to = $request->to;
    }

    $responseArray = $this->query()
      ->select('id', 'company_register_id', 'werbeaktion', 'sub_category', 'customerstate', 'created_at', DB::raw(
        "(SELECT count(*) FROM appointments
                          WHERE appointments.locationId = campaign_locations.id 
                          AND appointments.appointmentTypeId = 1
                          AND appointments.nextAppointmentId IS NULL
                          AND finished_at IS NULL
                        ) as new_customers_appointments"
      ))
      ->whereNotNull($columnName)
      ->when($from, function ($query) use ($from) {
        $query->where('created_at', '>=', $from);
      })
      ->when($to, function ($query) use ($to) {
        $query->where('created_at', '<=', $to);
      })
      ->get()
      ->groupBy($columnName);

    $data = [];

    foreach ($responseArray as $index => $response) {
      $data[] = array('categoryName' => $index, 'locationList' => $response);
    }

    return $data;
  }

  public function purgeLocationWithDomain($domain)
  {

    return DB::table($this->getTable())->where('domain', '=', $domain)->delete();
  }

  /**
   * Returns a location's customer state if given domain is matched
   * @param string $domain the domain to match
   * @return Location|null
   */
  public function findForCrawlerResultReport($domain)
  {
    return Location::where('domain', $domain)
      ->orWhere('homepage', $domain)
      ->select([
        'customerstate'
      ])
      ->first();
  }

  public function getPermissionOfRegistrationOptionsBlock()
  {
    return [
      PermissionType::COMPANY_DETAILS_REGISTRATION_OPTIONS_BLOCK_SHOW,
      PermissionType::COMPANY_DETAILS_REGISTRATION_OPTIONS_BLOCK_EDIT,
    ];
  }

  public function getPermissionOfWebsiteDataBlock()
  {
    return [
      PermissionType::COMPANY_DETAILS_WEBSITE_DATA_BLOCK_SHOW,
      PermissionType::COMPANY_DETAILS_WEBSITE_DATA_BLOCK_EDIT,
    ];
  }

  public function getPermissionOfRevocationORCancellationBlock()
  {
    return [
      PermissionType::COMPANY_DETAILS_REVOCATION_OR_CANCELLATION_BLOCK_SHOW,
      PermissionType::COMPANY_DETAILS_REVOCATION_OR_CANCELLATION_BLOCK_EDIT,
    ];
  }

  public function getPermissionOfCustomerDataBlock()
  {
    return [
      PermissionType::COMPANY_DETAILS_CUSTOMER_DATA_BLOCK_SHOW,
      PermissionType::COMPANY_DETAILS_CUSTOMER_DATA_BLOCK_EDIT,
    ];
  }

  public function getPermissionOfWerbreaktionBlock()
  {
    return [
      PermissionType::COMPANY_DETAILS_WERBREAKTION_BLOCK_SHOW,
      PermissionType::COMPANY_DETAILS_WERBREAKTION_BLOCK_EDIT,
    ];
  }

  public function getPermissionOfVerkaufsdatenBlock()
  {
    return [
      PermissionType::COMPANY_DETAILS_VERKAUFSDATEN_BLOCK_SHOW,
    ];
  }
}
