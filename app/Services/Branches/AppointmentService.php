<?php

/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use App\Entities\Branches\Appointment;
use App\Entities\Customers\Customer;
use App\Repositories\Branches\AppointmentRepository;
use App\Utils\DateTimeUtils;

class AppointmentService
{

  private $appointmentRepository;

  public function __construct(AppointmentRepository $appointmentRepository)
  {
    $this->appointmentRepository = $appointmentRepository;
  }

  /**
   * @param $id
   * @return Appointment|null
   */
  public function byId($id)
  {
    return $this->appointmentRepository->byId($id);
  }

  public function findAllOfLocation($locId)
  {
    return $this->appointmentRepository->findAllOfLocation($locId);
  }

  /**
   * @param $id
   * @param $sellerId
   * @param $seller
   * markiert einen Termin als Verkauft
   * @return Appointment
   */
  public function markAsSoldNow($id, $sellerId, $seller = null)
  {
    $app = $this->byId($id);
    $app->result = Appointment::RESULT_VERKAUFT;
    $app->verkauftAm = DateTimeUtils::nowAsString();
    $app->verkauftVon = $sellerId;
    $app->finished_at = $app->verkauftAm;
    $app->finishedUserId = $sellerId;
    $app->seller = $seller;
    $app->save();
    $this->markLocationAsCustomer($app);
    return $app;
  }

  public function markAsSoldNowWithSellerName($id, $name)
  {
    $app = $this->byId($id);
    $app->result = Appointment::RESULT_VERKAUFT;
    $app->verkauftAm = DateTimeUtils::nowAsString();
    $app->seller = $name;
    $app->finished_at = $app->verkauftAm;
    $app->save();
    $this->markLocationAsCustomer($app);
    return $app;
  }

  /**
   * quickly creates an appointment and returns it
   *
   * @param $locationId
   * @param $when
   * @param int $type
   * @return Appointment
   */
  public function createQuick($locationId, $when, $type = Appointment::TYPE_DEFAULT)
  {
    return $this->appointmentRepository->createQuick($locationId, $when, $type);
  }

  public function purgeAllCreatedByUser($userId)
  {
    $apps = $this->appointmentRepository->findAllCreatedByUser($userId);
    foreach ($apps as $app) {
      $this->appointmentRepository->purge($app);
    }
  }

  private function markLocationAsCustomer(Appointment $app)
  {
    /**
     * @var $loc Location
     */
    $loc = $app->location;
    if ($loc == null) return null;
    $loc->customerstate = Customer::STATE_CUSTOMER;
    $loc->save();
  }
}
