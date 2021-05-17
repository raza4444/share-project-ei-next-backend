<?php

/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use App\Entities\Branches\Appointment;
use App\Entities\Branches\LocationEvent;
use App\Entities\Core\InternUser;
use App\Http\Controllers\Intern\Duties\DutyRowController;
use App\Repositories\Branches\LocationEventRepository;
use App\Repositories\Core\UserRepository;
use App\Services\Tasks\BusinessCounterTaskEventService;

class LocationEventInterestService
{

  private $locationEventRepository;

  private $userRepository;

  private $businessCounterTaskEventService;

  private $dutyRowController;

  public function __construct(
    LocationEventRepository $locationEventRepository,
    BusinessCounterTaskEventService $businessCounterTaskEventService,
    UserRepository $userRepository,
    DutyRowController $dutyRowController
  ) {
    $this->locationEventRepository = $locationEventRepository;
    $this->userRepository = $userRepository;
    $this->businessCounterTaskEventService = $businessCounterTaskEventService;
    $this->dutyRowController = $dutyRowController;
  }

  public function handleInterestByEventId($id, $currentUserId, $to)
  {
    $app = $this->createAppointmentForEvent($id, $currentUserId, $to);
    $this->businessCounterTaskEventService->handleAfterAppointmentCreated($app);
    $event = $this->locationEventRepository->byIdActive($id);
    $user = $this->userRepository->byId($currentUserId);
    $this->sendMail($event, $to['appointmentAt'], $user);
  }

  /**
   * erzeugt einen neuen Termin fuer dieses Ereignis
   *
   * @param $eventId
   * @param $currentUserId
   * @param $to
   * @return Appointment
   */
  private function createAppointmentForEvent($eventId, $currentUserId, $to)
  {
    /**
     * @var $event LocationEvent
     */
    $event = $this->locationEventRepository->byId($eventId);

    $app = new Appointment();
    $app->locationId = $event->location->id;
    $app->eventId = $eventId;
    $app->createdUserId = $currentUserId;
    $app->when = $to['appointmentAt'];
    $app->appointmentTypeId = $to['appointmentTypeId'];
    $app->ansprechpartner_anrede = $to['ansprechpartner_anrede'];
    $app->ansprechpartner_vorname = $to['ansprechpartner_vorname'];
    $app->ansprechpartner_nachname = $to['ansprechpartner_nachname'];
    $app->preisinfo = $to['preisinfo'];

    if ($app->save()) {
      // Generate duty rows for new appointment
      $this->dutyRowController->createRowsAndTasksForNewAppointment($app->id, $event->location->id, null);
    }

    return $app;
  }

  private function sendMail(LocationEvent $event, $appointmentAt, InternUser $user)
  {
    $company = $event->location->title;
    $username = $user->username;
    $subject = "Neuer VK Termin EILD $appointmentAt durch 'Kalt' von Benutzer $username mit $company";
    mail('wameling@web.de', $subject, $subject);
  }
}
