<?php

/**
 * by stephan scheide
 */

namespace App\Services\Customers;

use App\Entities\Branches\AppointmentType;
use App\Entities\Branches\CompanyRegisterLocation;
use App\Entities\Branches\Location;
use App\Entities\Branches\LocationEmail;
use App\Entities\Customers\Customer;
use App\Entities\Customers\CustomerInfoDataFactory;
use App\Exceptions\EntityNotFoundException;
use App\Logging\LoggingClip;
use App\Repositories\Branches\LocationRepository;
use App\Repositories\Customers\CustomerRepository;
use App\Repositories\Customers\CustomerTokenRepository;
use App\Services\Branches\AppointmentService;
use App\Services\Branches\CompanyRegisterService;
use App\Services\Branches\GoogleGeoCodingService;
use App\Utils\StringUtils;
use Illuminate\Http\Request;

class CustomerRegistrationService
{
  private $customerRepository;
  private $customerTokenRepository;
  private $locationRepository;
  private $appointmentService;
  private $companyRegisterService;
  private $googleGeoCodingService;

  public function __construct(
    CustomerTokenRepository $customerTokenRepository,
    LocationRepository $locationRepository,
    CustomerRepository $customerRepository,
    AppointmentService $appointmentService,
    CompanyRegisterService $companyRegisterService,
    GoogleGeoCodingService $googleGeoCodingService
  ) {
    $this->customerTokenRepository = $customerTokenRepository;
    $this->customerRepository = $customerRepository;
    $this->locationRepository = $locationRepository;
    $this->appointmentService = $appointmentService;
    $this->companyRegisterService = $companyRegisterService;
    $this->googleGeoCodingService = $googleGeoCodingService;
  }

  /**
   * @param $token
   * @return Location|null
   * @throws EntityNotFoundException
   */
  public function findCustomerByToken($token)
  {
    $ct = $this->customerTokenRepository->findByToken($token);
    if ($ct == null) {
      throw EntityNotFoundException::byEntity('Kundentoken', $token);
    }
    $c = $ct->customer;
    if ($c == null) {
      throw EntityNotFoundException::byEntity('Kunde', "Token=$token");
    }
    $l = $this->locationRepository->findByIdComplete($c->id);
    if ($l == null) {
      throw EntityNotFoundException::byEntity('Unternehmen', 'id=' . $c->id);
    }
    return $l;
  }

  public function completeRegistration(Request $request)
  {
    $result = [];

    $clip = new LoggingClip('customer-registration', StringUtils::createGUID());
    $googleGeoCodingclip = new LoggingClip('google-geo-coding', StringUtils::createGUID());
    $clip->info('*** BEGIN Neue Registrierung');

    try {
      $acc = new CustomerCompletedRegisrationRequestAccessor($request);
      $clip->info('Accessor erzeugt');
      $clip->info('JSON-Daten');
      $clip->info(json_encode($acc->getJSONData()));
      $token = $acc->getToken();
      $clip->info("token: $token");

      //Unternehmen/Kunde finden
      $loc = $this->findCustomerByToken($token);
      $clip->info("Kunde: " . $loc->id);
      $result['locationid'] = $loc->id;
      $result['location'] = $loc;

      //Stammdaten übernehmen
      $vd = $acc->getVisibleData();
      $loc->title = $vd['firma'];
      $loc->street = $vd['strasse'] . ' ' . $vd['hnr'];
      $loc->zip = $vd['plz'];
      $loc->city = $vd['ort'];
      $tmp = explode(' ', trim($vd['name']));
      if (count($tmp) == 1) {
        $loc->ansprechpartner_nachname = $tmp[0];
        $clip->info('Setze AP-Nachname auf ' . $loc->ansprechpartner_nachname);
      } else {
        $loc->ansprechpartner_vorname = $tmp[0];
        $loc->ansprechpartner_nachname = $tmp[1];
        $clip->info('Setze AP-Vorname auf ' . $loc->ansprechpartner_vorname);
        $clip->info('Setze AP-Nachname auf ' . $loc->ansprechpartner_nachname);
      }

      //EMails
      $email = $vd['email'];
      if (!StringUtils::isTooShort($email, 5)) {
        $clip->info("EMail gefunden: $email - wird angelegt");
        $this->locationRepository->addEmail($loc, $vd['email']);
        $loc->email = $email;
      }
      $seitenmails = $vd['seitenemail'];
      if (!StringUtils::isTooShort($seitenmails, 5)) {
        $tmp = explode(',', $seitenmails);
        $clip->info('EMail für neue Seite - Anzahl: ' . count($tmp));
        foreach ($tmp as $email) {
          $this->locationRepository->addEmail($loc, $email, LocationEmail::TYPE_PRODUCT);
          $clip->info("Seitenemail $email angelegt");
        }
      }

      //Telefonnummern
      if (!StringUtils::isTooShort($vd['telefon1'], 5)) {
        $this->locationRepository->addPhoneNumber($loc, $vd['telefon2']);
      }
      if (!StringUtils::isTooShort($vd['telefon2'], 5)) {
        $this->locationRepository->addPhoneNumber($loc, $vd['telefon2']);
      }

      //Andere Daten übernehmen
      $infoDataList = CustomerInfoDataFactory::createManyFromRegistrationData($loc->id, $vd);
      foreach ($infoDataList as $infoData) {
        $this->customerRepository->createInfoData($infoData);
      }

      //Status setzen
      $loc->customerstate = Customer::STATE_CUSTOMER;

      // Send data to company-register with action 'gesperrt kalt kontakt wegen kunde'
      // Minimum required fields
      if (isset($loc->domain) && isset($loc->title) && isset($loc->city) && isset($loc->zip) && isset($loc->street)) {

        try {

          $clip->info('Sende neues Unternehmen zu zentralem Unternehmensregister');

          $companyRegisterLoc = new CompanyRegisterLocation();
          $companyRegisterLoc->aktion = CompanyRegisterLocation::ACTION_NEW_LOCATION;
          $companyRegisterLoc->name = $loc->title;
          $companyRegisterLoc->domain = $loc->domain;
          $companyRegisterLoc->ort = $loc->city;
          $companyRegisterLoc->plz = $loc->zip;
          $companyRegisterLoc->strasse = $loc->street;

          // Not essentially required
          if (isset($loc->email)) {
            $companyRegisterLoc->email =  $loc->email;
          }
          if (isset($loc->country)) {
            $companyRegisterLoc->land =  $loc->country;
          }
          if (isset($loc->phoneNumber)) {
            $companyRegisterLoc->telefon =  $loc->phoneNumber;
          }

          $res = $this->companyRegisterService->create($companyRegisterLoc);
          $resBody = json_decode($res->getBody());
          $clip->info($res->getBody());
          $clip->info('Erfolgreich gesendet und Antwort erhalten.');

          // Assign response->locationId to companyRegisterId for reference
          if (isset($resBody->id)) {
            $clip->info('Verlinke Unternehmen ' . $loc->id . ' mit zentraler Unternehmensregister-ID ' . $resBody->id);
            $loc->company_register_id = $resBody->id;
          }
        } catch (\Throwable $th) {
          $clip->error('An exception was thrown while sending new location to company-register.');
          $clip->error($th);
        }
      } else {
        $clip->info('Für das Anlegen des Unternehmens im zentralen Unternehmensregister muessen alle Pflichtfelder gesetzt sein. Überspringe Abgleich.');
      }

      $loc->save();
      $clip->info('Unternehmen ' . $loc->id . ' gespeichert');

      // Setting coordinates for new customer
      if ($loc->street && $loc->zip && $loc->city) {
        $googleGeoCodingclip->info('New customer registration completed (' . $loc->id . '). Setting coordinates');
        $this->googleGeoCodingService->setCoordinatesForCustomer($loc);

      } else {
        $googleGeoCodingclip->info('New customer registration completed (' . $loc->id . '). Can\'t set coordinates due to invalid address.');
      }

      //$tmp = explode('/', $vd['zweiterTerminTag']);
      //$date = DateTimeUtils::makeDataDateFromComponents($tmp[2], $tmp[1], $tmp[0]);
      //$time = $vd['zweiterTerminUhrzeit'] . ':00';

      //$dateTime = $date . ' ' . $time;

      //$clip->info('Erzeuge Beratungstermin am ' . $dateTime);
      //$app = $this->appointmentService->createQuick($loc->id, $dateTime, Appointment::TYPE_CONSULTANT);
      //$clip->info('Termin erzeugt mit ID ' . $app->id);
      //$clip->info('Am Termin ein paar Daten nachtraeglich anhaengen ' . $vd['wem']);
      //$app->seller = $vd['wem'];
      //$app->save();
      //$clip->info('Zweite Speicherung erfolgreich');

      //$result['appid'] = $app->id;

      //Alle Termine auf erfolgreich setzen
      $apps = $this->appointmentService->findAllOfLocation($loc->id);

      foreach ($apps as $a) {
        // if ($a->typ == Appointment::TYPE_DEFAULT && ($a->nextAppointmentId === null || $a->nextAppointmentId == 0)) {
        if ($a->appointmentTypeId === AppointmentType::TYPE_SALES_APPOINTMENT && ($a->nextAppointmentId === null || $a->nextAppointmentId == 0)) {
          $clip->info("Setzte Appointment mit ID " . $a->id . " auf Verkauft");
          $this->appointmentService->markAsSoldNowWithSellerName($a->id, $vd['wem']);
          $clip->info('Gespeichert');
        }
      }

      $clip->info("Abschluss Registrierung");

      return $result;
    } catch (\Throwable $ex) {
      $clip->error('unbehandelte Ausnahme');
      $clip->exception($ex);
      throw $ex;
    }
  }
}
