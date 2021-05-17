<?php

/**
 * by Samuel Leicht
 */

namespace App\Services\Duties;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Repositories\Duties\DutyRowRepository;

class DutyRowService
{
  private $dutyTaskService;
  private $dutyRowRepository;

  public function __construct(
    DutyTaskService $dutyTaskService,
    DutyRowRepository $dutyRowRepository
  ) {
    $this->dutyTaskService = $dutyTaskService;
    $this->dutyRowRepository = $dutyRowRepository;
  }

  /**
   * returns a duty row including addional info (location, minFinishedCount) for a given row id
   * @param $dutyRowId the id of the row
   * @return Object
   */
  public function getRowWithAddInfo($dutyRowId)
  {
    return DB::table('duty_rows AS dr')
      ->where('dr.id', $dutyRowId)
      ->join('campaign_locations AS cl', 'dr.locationId', 'cl.id')
      ->join('duty_row_templates AS drt', 'drt.id', 'dr.dutyRowTemplId')
      ->select([
        'dr.*',
        'drt.minFinishedTasksToCompl',
        'drt.name AS rowTemplTitle',
        'cl.id',
        'cl.title AS locationTitle'
      ])
      ->get()[0];
  }

  /**
   * creates a duty row for a follow up
   * @param $parentRowId the id of the parent duty row
   * @param $dutyBlockId the id of the associated duty block
   * @param $dutyRowTemplId the id of the associated duty row template
   */
  public function createFollowUpRow($parentRowId, $dutyBlockId, $dutyRowTemplId)
  {
    $q = '
    INSERT INTO duty_rows (parentDutyRowId, dutyRowTemplId, dutyBlockId, appointmentId, locationId)
    SELECT ' . $parentRowId . ',' . $dutyRowTemplId . ', ' . $dutyBlockId . ', dr.appointmentId, dr.locationId FROM duty_rows AS dr
    INNER JOIN duty_row_templates AS drt ON drt.id = dr.dutyRowTemplId
    WHERE dr.id = ' . $parentRowId;

    DB::insert($q);

    return DB::table('duty_rows')
      ->where('id', DB::getPdo()->lastInsertId())
      ->get()
      ->first();
  }

  /**
   * creates a duty row after a new appointment was created
   *
   * @param $appId the id of the appointment
   * @param $locId the id of the location
   */
  public function createRowsAndTasksForNewAppointment($appId, $locId, $callerRowId)
  {
    // Duty rows
    $q = 'INSERT INTO duty_rows (appointmentId, locationId, dutyRowTemplId, dutyBlockId) 
          SELECT ' . $appId . ', ' . $locId . ', drt.id, dbrt.dutyBlockId
          FROM duty_row_templates AS drt 
          INNER JOIN duty_blocks_rows_templ as dbrt ON dbrt.dutyRowTemplId = drt.id 
          INNER JOIN duty_rows_templ_trigger as drtt ON drtt.dutyRowTemplId = drt.id 
          INNER JOIN appointments as a ON a.id = ' . $appId . '
          INNER JOIN appointment_types as at ON at.id = a.appointmentTypeId
          INNER JOIN duty_triggers as dt ON dt.id = drtt.dutyTriggerId 
          AND dt.appointment_creation = 1
          AND at.id = dt.appointmentTypeId
          WHERE (CASE drt.createOnce
          WHEN 1 THEN 
            (SELECT COUNT(id) FROM duty_rows 
            WHERE locationId = ' . $locId . ' 
            AND dutyRowTemplId = dbrt.dutyRowTemplId) = 0
          ELSE 1 END)
          AND (CASE WHEN dt.excludedAppointmentTypeId IS NOT NULL THEN
            (SELECT COUNT(id) FROM appointments
            WHERE locationId = ' . $locId . ' 
            AND appointmentTypeId = dt.excludedAppointmentTypeId) = 0
          ELSE 1 END)';

    DB::insert($q);

    // Delete old duplicate rows
    $this->finishOldDuplicateRowsForLocationForNewApp($callerRowId);

    // DutyTasks for rows
    $this->dutyTaskService->createTasksForFollowUpRowForAppointment($appId, $locId);
  }

  /**
   * deletes old duty rows of same kind of same location
   * after new appointment was created and skips callerRowId
   */
  public function finishOldDuplicateRowsForLocationForNewApp($callerRowId)
  {
    // $q = 'DELETE t1 FROM duty_rows as t1
    //       INNER JOIN duty_rows t2
    //       WHERE t1.id < t2.id 
    //       AND t1.dutyRowTemplId = t2.dutyRowTemplId
    //       AND t1.dutyBlockId = t2.dutyBlockId
    //       AND t1.locationId = t2.locationId
    //       AND t1.done = 0'. (isset($callerRowId) ? ' AND t1.id != '. $callerRowId : null);

    $q = 'UPDATE duty_rows AS t1
          INNER JOIN duty_rows t2
          SET t1.done = 1
          WHERE t1.id < t2.id 
          AND t1.dutyRowTemplId = t2.dutyRowTemplId
          AND t1.dutyBlockId = t2.dutyBlockId
          AND t1.locationId = t2.locationId
          AND t1.done = 0' . (isset($callerRowId) ? ' AND t1.id != ' . $callerRowId : null);

    DB::delete($q);
  }

  /**
   * returns all duty rows for a specific duty block
   *
   * @return array
   */
  public function dutyRowsForDutyBlock($blockId)
  {
    $q = "
        SELECT dr.id, dr.name FROM duty_row_templates as dr
        INNER JOIN duty_blocks_rows as dbr
        ON dbr.id_duty_block = " . $blockId;

    return DB::select($q);
  }

  /**
   * returns all duty rows including linked data for a specific duty block
   * WHEN 'recurring_contact' THEN DATE_ADD(cl.wiederkontaktAm, INTERVAL drtc.colDateOffset HOUR)
   * // AND (a.when > NOW()) > maybe for future use
   * @return array
   */
  public function allDataForBlock($blockId)
  {
    $q = "
    SELECT cl.title, 
    @locationId := cl.id AS locationId, 
    dcdt.keyName AS keyName,
    dr.appointmentId AS appointmentId, 
    drt.name, 
    dr.id, 
    u1.username AS agentUserName1,
    u2.username AS agentUserName2,
    u3.username AS agentUserName3,
    u4.username AS assignedUserName,
    CASE WHEN dr.updated_by IS NOT NULL THEN u5.username ELSE NULL END AS updatedByUserName,
    dr.assignedUserId,
    dr.updated_at,
    @calculated_when:= (CASE keyName 
    WHEN 'creation_date' THEN DATE_ADD(dr.created_at, INTERVAL drtc.colDateOffset HOUR)
    ELSE DATE_ADD(
      (
        SELECT a2.when FROM appointments AS a2 , appointment_types as at2 
        WHERE at2.id = a2.appointmentTypeId AND a2.locationId =  cl.id  AND (a2.result IS NULL OR a2.result != 20) AND at2.name = dcdt.keyName
        ORDER BY a2.id DESC
        LIMIT 1
      ), INTERVAL drtc.colDateOffset HOUR
    )
    END) AS `calculated_when`,
    
    CASE WHEN @calculated_when IS NULL THEN dr.created_at ELSE @calculated_when END AS `when`
    
    FROM duty_rows AS dr
    INNER JOIN duty_row_templates as drt ON drt.id = dr.dutyRowTemplId
    INNER JOIN duty_rows_templ_cols as drtc ON drt.id = drtc.rowTemplId
    INNER JOIN duty_column_date_types as dcdt ON drtc.colDateTypeId = dcdt.id
    INNER JOIN campaign_locations AS cl ON cl.id = dr.locationId
    LEFT JOIN users AS u1 ON u1.id = cl.agentId1
    LEFT JOIN users AS u2 ON u2.id = cl.agentId2
    LEFT JOIN users AS u3 ON u3.id = cl.agentId3
    LEFT JOIN users AS u4 ON u4.id = dr.assignedUserId
    LEFT JOIN users AS u5 ON u5.id = dr.updated_by
    LEFT JOIN duty_rows AS dr2 ON dr2.id = dr.parentDutyRowId
    WHERE dr.dutyBlockId = " . $blockId . "
    AND dr.done = 0
    ORDER BY `when` ASC";
    return DB::select($q);
  }


  public function getDutyRowsForUserBreaks($timePeriod)
  {
    $breakTime = array();
    if ($timePeriod == 'today') {
      $dutyRow = $this->dutyRowRepository->dutyRowsInclDataForTodayBreaks();
    } else if ($timePeriod == 'current-week') {
      $dutyRow = $this->dutyRowRepository->dutyRowsInclDataForCurrentWeekBreaks();
    } else if ($timePeriod == 'current-month') {
      $dutyRow = $this->dutyRowRepository->dutyRowsInclDataForCurrentMonthBreaks();
    }

    $specificUsersData = array();
    foreach ($dutyRow as $row) {
      $specificUsersData[$row->userId . "-" . $row->username][]  =  $row;
    }

    foreach ($specificUsersData as $index => $userData) {
      $userIndex = explode("-", $index);
      $user = array('userId' => $userIndex[0], "username" => $userIndex[1]);
      $breakTimeOfUser = $this->allUserBreakTime($user, $userData);
      if (count($breakTimeOfUser) > 0) {
        $breakTime[$user['userId']] = $breakTimeOfUser;
      }
    }
    return $this->resetArrayIndexes($breakTime);
  }

  /**
   * @param array $user
   * @param array $specificUserData
   * @return void
   */

  private function allUserBreakTime(array $user, array $specificUserData)
  {

    $breakTimeArray = array();
    $breakTimeDistributionWithDate  = [];
    foreach ($specificUserData as $key => $data) {
      $next = $key + 1;
      if (array_key_exists($key, $specificUserData) && array_key_exists($next, $specificUserData)) {
        $date1 =  Carbon::parse($specificUserData[$key]->finishedAt);
        $date2 =  Carbon::parse($specificUserData[$key + 1]->startedAt);
        $dateDifference = $date2->diff($date1);
        if (Carbon::parse($date1->format('Y-m-d'))->equalTo(Carbon::parse($date2->format('Y-m-d'))) && (int) $dateDifference->format('%I') > 1 && $date2->gt($date1) ) {
          $breakTimeDistributionWithDate[$date2->format('Y-m-d')][] = $this->findBreakTimeBetweenTwoDates($date1, $date2);
        }
      }
    }

    $totalDailyTimeBreak = [];
    $totalDailyBreakTimeDay = 0;
    $totalDailyBreakTimeHours = 0;
    $totalDailyBreakTimeMinutes = 0;
    $countPause = 0;
    foreach ($breakTimeDistributionWithDate as $breakTimeArray) {
      $totalMinutes = 0;
      $totalHours  = 0;
      $totalDays  = 0;
      $date = '';
      foreach ($breakTimeArray as $breakTime) {
        $date = $breakTime->date;
        $totalMinutes = $totalMinutes + $breakTime->minute;
        $totalHours = $totalHours + $breakTime->hours;
        $totalDays = $totalDays + $breakTime->day;
      }

      $minutes = $totalMinutes % 60;
      $hours = (($totalHours) + ($totalMinutes / 60)) % 24;
      $day = $totalDays + $totalHours / 24;

      $totalDailyTimeBreak[] = (object) array(
        "totalDays" => floor($day),
        "totalHours" => floor($hours),
        "totalMinutes" => $minutes,
        "userId" => $user['userId'],
        "username" => $user['username'],
        "date" => $date,
        'list' => $breakTimeArray
        
      );

      $totalDailyBreakTimeMinutes = $totalDailyBreakTimeMinutes + $minutes;
      $totalDailyBreakTimeHours = $totalDailyBreakTimeHours + floor($hours);
      $totalDailyBreakTimeDay = $totalDailyBreakTimeDay + floor($day);
      $countPause = $countPause + count($breakTimeArray);
   
    }


    $minutes = $totalDailyBreakTimeMinutes % 60;
    $hours = (($totalDailyBreakTimeHours) + ($totalDailyBreakTimeMinutes / 60)) % 24;
    $day = $totalDailyBreakTimeDay + ($totalDailyBreakTimeHours) / 24;
  
    if(count($totalDailyTimeBreak) > 0) {
    return array(
    'userId'=>$user['userId'] ,
    'totalDays'=> floor($day),
    'totalHours'=>  floor($hours),
    'totalMinutes'=> $minutes,
    'dailyBreakTimes'=> $totalDailyTimeBreak,
    'countPause' => $countPause
     );
    } else {
      return [];
    }
  
  }

  private function findBreakTimeBetweenTwoDates($date1, $date2)
  {
    $totalDuration = $date2->diff($date1);
    return (object) array(
      'day' => $totalDuration->format('%d'),
      'hours' => $totalDuration->format('%H'),
      'minute' => $totalDuration->format('%I'),
      'from' => $date1->format('H:i'),
      'to' => $date2->format('H:i'),
      'date' => $date2->format('Y-m-d'),
    );
  }

  private function resetArrayIndexes($array)
  {
    $normalizedArray = [];
    foreach ($array as $value) {
      if($value != null) {
      $normalizedArray[] = $value;
    }
    }
    return $normalizedArray;
  }
}
