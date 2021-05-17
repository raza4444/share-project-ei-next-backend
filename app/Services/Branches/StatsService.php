<?php

/**
 * by stephan scheide
 */

namespace App\Services\Branches;


use App\Repositories\Branches\AppointmentRepository;
use Illuminate\Support\Facades\DB;

class StatsService
{

  private $appointmentRepository;

  public function __construct(AppointmentRepository $appointmentRepository)
  {
    $this->appointmentRepository = $appointmentRepository;
  }

  public function getUserStatsCold($date)
  {
    //Heute
    $fines = $date;
    //$fines = '2019-11-25';

    //Anzahl neue Termine
    $map = [];

    $q = 'select count(*) as cc,u.username from appointments a 
        inner join users u on a.createdUserId = u.id 
        inner join appointment_types at on at.id = a.appointmentTypeId
        where at.name = \'sales_appointment\'
        and a.eventId>0 and (a.preAppointmentId is null) 
        and date(a.created_at)=? 
        group by u.username 
        order by u.username asc';

        // GROUPING BY LOCATION > SUSPENDED SINCE DOUBLE SALES APPOINTMENTS SHOULD GET AVOIDED
        // SELECT COUNT(t2.id) AS cc, t2.username FROM (SELECT a.id, u.username FROM appointments a
        // INNER JOIN appointment_types AS at ON at.id = a.appointmentTypeId
        // INNER JOIN users AS u ON u.id = a.createdUserId
        // WHERE at.name = \'sales\' AND a.eventId > 0 AND(a.preAppointmentId IS NULL) AND DATE(a.created_at) = ?
        // GROUP BY a.locationId) AS t2 GROUP BY t2.username ORDER BY t2.username ASC

    $list = DB::select($q, [$fines]);
    foreach ($list as $row) {
      $map[$row->username] = [$row->cc, 0];
    }

    //Anzahl Verkaeufe
    $q = 'select id from appointments where date(verkauftAm)=? and result=0 and eventId>0';
    $list = DB::select($q, [$fines]);
    foreach ($list as $row) {
      $id = $row->id;
      //echo $id . "\r\n";
      $org = $this->appointmentRepository->findOriginalAppointmentOf($id);
      //echo "\r\n";
      $u = $org && $org->creator ? $org->creator->username : 'unbekannt';
      if (!array_key_exists($u, $map)) $map[$u] = [0, 0];
      $map[$u][1]++;
    }

    /*
        $q = 'select count(*) as cc,u.username from appointments a inner join users u on a.verkauftVon = u.id where a.eventId>0 and date(a.verkauftAm)=? and result=0  group by u.username order by u.username asc';
        $list = DB::select($q, [$fines]);
        foreach ($list as $row) {
            $u = $row->username;
            if (!array_key_exists($u, $map)) $map[$u] = [0, 0];
            $map[$u][1] = $row->cc;
        }
        */

    //Aufbereitung als flache Liste
    $result = [];
    foreach ($map as $username => $counts) {
      $result[] = ['username' => $username, 'appointments' => $counts[0], 'sales' => $counts[1]];
    }

    return $result;
  }

  public function getUserStatsWarm($date)
  {
    //Heute
    $fines = $date;
    //$fines = date('Y-m-d');

    $map = [];

    //Erzeuge Termine
    $q = 'select 
        l.werbeaktion,count(*) as anzahl from appointments a 
        inner join campaign_locations l on l.id=a.locationId 
        inner join appointment_types as at on at.id = a.appointmentTypeId
        where at.name = \'sales_appointment\'
        and a.eventId=0 
        and (a.preAppointmentId is null) 
        and date(a.created_at)=? 
        and (l.werbeaktion is not null) 
        group by l.werbeaktion';

    $list = DB::select($q, [$fines]);
    foreach ($list as $row) {
      $w = $row->werbeaktion;
      if (!array_key_exists($w, $map)) $map[$w] = [0, 0];
      $map[$w][0] = $row->anzahl;
    }

    //Verkaeufe
    $q = 'select 
        l.werbeaktion,count(*) as anzahl from appointments a 
        inner join campaign_locations l on l.id=a.locationId 
        inner join appointment_types as at on at.id = a.appointmentTypeId
        where at.name = \'sales_appointment\'
        and a.eventId=0 
        and date(a.verkauftAm)=? 
        and (a.result=0) 
        and (l.werbeaktion is not null) 
        group by l.werbeaktion';

    $list = DB::select($q, [$fines]);
    foreach ($list as $row) {
      $w = $row->werbeaktion;
      if (!array_key_exists($w, $map)) $map[$w] = [0, 0];
      $map[$w][1] = $row->anzahl;
    }

    $result = [];
    foreach ($map as $w => $data) {
      $result[] = [$w, $data[0], $data[1]];
    }

    return $result;
  }
}
