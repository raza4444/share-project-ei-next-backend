<?php

namespace App\Http\Controllers\Intern\Appointments;

use App\Entities\Appointments\PossibleAppointmentsAmount;
use App\Http\Controllers\AbstractInternController;
use App\Services\Appointments\AppointmentsPossibleService;
use Illuminate\Http\Request;

class PossibleAppointmentsController extends AbstractInternController
{

  private $appointmentsPossibleService;

  public function __construct(
    AppointmentsPossibleService $appointmentsPossibleService
  ) {
    $this->appointmentsPossibleService = $appointmentsPossibleService;
  }

  public function updateDefaultWeek(Request $request, $appointmentTypeId)
  {
    $body = $request->json()->all();
    foreach ($body as $row) {
      $e = new PossibleAppointmentsAmount();
      $e->appointmentTypeId = $appointmentTypeId;
      $e->hour = $row['hour'];
      $e->minute = $row['minute'];
      $e->weekday = $row['weekday'];
      $e->amount = $row['amount'];
      $e->default = true;
      $this->appointmentsPossibleService->save($e);
    }
    return $this->noContent();
  }

  public function updateWeek(Request $request, $appointmentTypeId, $ymd)
  {
    $body = $request->json()->all();
    foreach ($body as $row) {
      $e = new PossibleAppointmentsAmount();
      $e->appointmentTypeId = $appointmentTypeId;
      $e->hour = $row['hour'];
      $e->minute = $row['minute'];
      $e->weekday = $row['weekday'];
      $e->amount = $row['amount'];
      $e->default = false;
      $e->monday = $ymd;
      $this->appointmentsPossibleService->save($e);
    }
    return $this->noContent();
  }

  public function getDefaultWeek($appointmentTypeId)
  {
    $arr = $this->appointmentsPossibleService->findDefaultWeekData($appointmentTypeId);
    return $this->json(200, $arr);
  }

  public function getWeekYmd($appointmentTypeId, $ymd)
  {
    $arr = $this->appointmentsPossibleService->findWeekData($appointmentTypeId, $ymd);
    return $this->json(200, $arr);
  }

  public function getEffectiveWeekYmd($appointmentTypeId, $ymd)
  {
    $defaultWeek = $this->appointmentsPossibleService->findDefaultWeekData($appointmentTypeId);
    $week = $this->appointmentsPossibleService->findWeekData($appointmentTypeId, $ymd);

    /**
     * @var $e PossibleAppointmentsAmount
     */
    $arr = [];
    $keys = [];
    foreach ($week as $e) {
      $key = $e->weekday . ',' . $e->hour . ',' . $e->minute;
      $arr[] = $e;
      $keys[] = $key;
    }

    foreach ($defaultWeek as $e) {
      $key = $e->weekday . ',' . $e->hour . ',' . $e->minute;
      if (in_array($key, $keys)) continue;
      $arr[] = $e;
    }

    return $this->json(200, $arr);
  }
}
