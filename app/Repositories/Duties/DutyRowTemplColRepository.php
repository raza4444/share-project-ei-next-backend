<?php

/**
 * by Samuel Leicht
 */

namespace App\Repositories\Duties;

use App\Entities\Duties\DutyRowTemplColumn;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Facades\DB;

class DutyRowTemplColRepository extends AbstractRepository
{

  public function __construct()
  {
    parent::__construct(DutyRowTemplColumn::class);
  }

  /**
   * deletes all columns for a specific row template
   *
   * @param $rowTemplId the id of the row template
   */
  public function deleteAllColumnsOfRowTempl($rowTemplId)
  {
    DB::table('duty_rows_templ_cols')
    ->where('rowTemplId', $rowTemplId)
    ->delete();
  }
}
