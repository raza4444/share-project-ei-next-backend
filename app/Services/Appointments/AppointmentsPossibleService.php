<?php

namespace App\Services\Appointments;

use App\Entities\Appointments\PossibleAppointmentsAmount;

class AppointmentsPossibleService
{

  public function findDefaultWeekData($appointmentTypeId)
  {
    return PossibleAppointmentsAmount::query()
      ->where('appointmentTypeId', $appointmentTypeId)
      ->where('default', '=', 1)
      ->get();
  }

  public function findWeekData($appointmentTypeId, $ymd)
  {
    return PossibleAppointmentsAmount::query()
      ->where('appointmentTypeId', $appointmentTypeId)
      ->where('default', '=', 0)
      ->where('monday', '=', $ymd)
      ->get();
  }

  public function save(PossibleAppointmentsAmount $a)
  {
    return $a->default ? $this->saveDefault($a) : $this->saveForWeek($a);
  }

  private function deleteDefault($appointmentTypeId, PossibleAppointmentsAmount $a)
  {
    PossibleAppointmentsAmount::query()
      ->where('appointmentTypeId', $appointmentTypeId)
      ->where('default', '=', 1)
      ->where('hour', '=', $a->hour)
      ->where('minute', '=', $a->minute)
      ->where('weekday', '=', $a->weekday)
      ->delete();
  }

  private function saveDefault(PossibleAppointmentsAmount $a)
  {
    $this->deleteDefault($a->appointmentTypeId, $a);

    if ($a->amount != -1) {
      $a->monday = null;
      $a->default = 1;
      $a->save();
    }
    return $a;
  }

  private function deleteForWeek($appointmentTypeId, PossibleAppointmentsAmount $a)
  {
    PossibleAppointmentsAmount::query()
      ->where('appointmentTypeId', $appointmentTypeId)
      ->where('default', '=', 0)
      ->where('hour', '=', $a->hour)
      ->where('minute', '=', $a->minute)
      ->where('monday', '=', $a->monday)
      ->where('weekday', '=', $a->weekday)
      ->delete();
  }

  private function saveForWeek(PossibleAppointmentsAmount $a)
  {
    $this->deleteForWeek($a->appointmentTypeId, $a);

    if ($a->amount != -1) {
      $a->default = 0;
      $a->save();
    }
    return $a;
  }
}
