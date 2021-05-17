<?php

/**
 * by Samuel Leicht
 */

namespace App\Services\Duties;

use Illuminate\Support\Facades\DB;

class DutyBlockService
{

  public function __construct()
  {
  }

  /**
   * returns all duty blocks including their row count
   *
   * @return Array
   */
  public function getAllWithRowCount()
  {
    $q = "
      SELECT
      db.id,
      db.name
      FROM duty_block AS db
      ORDER BY db.pos ASC";


    $q = "
    SELECT 
    db.id,
      db.name,
      COUNT(duty_rows.id) AS rowCount
    FROM duty_block AS db
      LEFT OUTER JOIN (SELECT dr.id, dr.dutyBlockId, dr.done FROM duty_rows AS dr
          INNER JOIN duty_row_templates as drt ON drt.id = dr.dutyRowTemplId
          INNER JOIN duty_rows_templ_cols as drtc ON drt.id = drtc.rowTemplId
          INNER JOIN duty_column_date_types as dcdt ON drtc.colDateTypeId = dcdt.id
          INNER JOIN campaign_locations AS cl ON cl.id = dr.locationId
          LEFT JOIN duty_rows AS dr2 ON dr2.id = dr.parentDutyRowId
          WHERE dr.done = 0
                       
                       AND (CASE WHEN (CASE dcdt.keyName 
      WHEN 'creation_date' THEN DATE_ADD(dr.created_at, INTERVAL drtc.colDateOffset + 1 HOUR)
      ELSE DATE_ADD(
        (
          SELECT a2.when FROM appointments AS a2 , appointment_types as at2 
          WHERE at2.id = a2.appointmentTypeId AND a2.locationId =  cl.id  AND (a2.result IS NULL OR a2.result != 20) AND at2.name = 			dcdt.keyName
          ORDER BY a2.id DESC
          LIMIT 1
        ), INTERVAL drtc.colDateOffset HOUR
      )
      END) IS NULL THEN DATE_ADD(dr.created_at, INTERVAL 1 HOUR) ELSE (CASE dcdt.keyName 
      WHEN 'creation_date' THEN DATE_ADD(dr.created_at, INTERVAL drtc.colDateOffset + 1 HOUR)
      ELSE DATE_ADD(
        (
          SELECT a2.when FROM appointments AS a2 , appointment_types as at2 
          WHERE at2.id = a2.appointmentTypeId AND a2.locationId =  cl.id  AND (a2.result IS NULL OR a2.result != 20) AND at2.name = 			dcdt.keyName
          ORDER BY a2.id DESC
          LIMIT 1
        ), INTERVAL drtc.colDateOffset HOUR
      )
      END) END) < DATE_ADD(NOW(), INTERVAL 2 HOUR)
                       
		) duty_rows ON duty_rows.dutyBlockId = db.id
      GROUP BY db.id,db.name
      ORDER BY db.pos ASC";

    return DB::select($q);
  }
}
