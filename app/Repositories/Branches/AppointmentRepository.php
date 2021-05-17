<?php

/**
 * by stephan scheide
 */

namespace App\Repositories\Branches;


use App\Entities\Branches\Appointment;
use App\Repositories\AbstractRepository;
use App\Utils\DateTimeUtils;
use App\ValueObjects\Core\FieldOrders;
use Illuminate\Database\Eloquent\Builder;
use App\Entities\Core\PermissionType;

class AppointmentRepository extends AbstractRepository
{

  public function __construct()
  {
    parent::__construct(Appointment::class);
  }

  public function findOfDay($ymd, $appointmentTypeId, $typ = 0)
  {
    $start = $ymd . ' 00:00:00';
    $end = $ymd . ' 23:59:59';

    $query = Appointment::query();

    if (isset($appointmentTypeId)) {
      $query->where('appointmentTypeId', $appointmentTypeId);
    }

    return $query->whereNull('nextAppointmentId')
      ->where('when', '>=', $start)
      ->where('when', '<=', $end)
      ->where('typ', '=', 0)
      ->whereRaw('( (result is null) or ( (result<>' . Appointment::RESULT_GESCHEITERT . ') and (result<>' . Appointment::RESULT_VERSCHOBEN . ') ))')
      ->get();
  }

  /**
   * Liefert alle Termine zu diesem Unternehmen
   *
   * @param $locationId
   * @return Appointment[]
   */
  public function findAllOfLocation($locationId)
  {
    return $this->query()
    ->where('locationId', '=', $locationId)
    ->with('appointmentType')
    ->get();
  }

  public function findFiltered(BasicAppointmentFilter $filter, FieldOrders $orders, $matchers = [])
  {

    $query = Appointment::query();

    $now = date('Y-m-d H:i:s');

    if ($filter->year != null) {
      $query->where('created_at', '>=', $filter->year . '-01-01 00:00:00');
      $query->where('created_at', '<=', $filter->year . '-12-31 23:59:59');
    }

    if (count($filter->years) > 0) {
      $raw = '';
      foreach ($filter->years as $year) {
        $year = $year * 1;
        $raw .= "(year(created_at)=$year) or";
      }
      $raw = substr($raw, 0, -3);
      $query->whereRaw("($raw)");
    }

    if (count($filter->whenyears) > 0) {
      $raw = '';
      foreach ($filter->whenyears as $year) {
        $year = $year * 1;
        $raw .= "(year(`when`)=$year) or";
      }
      $raw = substr($raw, 0, -3);
      $query->whereRaw("($raw)");
    }

    if ($filter->onlyEmptyResult) {
      $query->whereNull('result');
    }

    if ($filter->onlyGone) {
      $query->where('when', '<=', $now);
    }

    if ($filter->onlyUpcoming) {
      $query->where('when', '>=', $now);
    }

    $query->where('typ', '=', $filter->type);

    if ($filter->werbeaktion != null) {
      $query->whereHas('location', function ($query) use ($filter) {
        $query->where('werbeaktion', '=', $filter->werbeaktion);
      });
    }

    if ($filter->skip !== null) {
      $query->skip($filter->skip);
    }

    if ($filter->top !== null) {
      $query->take($filter->top);
    }

    $query->with('location');

    $query->whereHas('location', function (Builder $query) {
      $query
        ->whereNull('deleted_at');
    });

    if ($filter->search !== null && strlen($filter->search) > 0) {
      $pattern = '%' . $filter->search . '%';
      $query->whereHas('location', function (Builder $query) use ($pattern) {
        $query
          ->where('title', 'like', $pattern)
          ->orWhere('phoneNumber', 'like', $pattern)
          ->orWhere('zip', 'like', $pattern)
          ->orWhere('city', 'like', $pattern)
          ->orWhere('id', 'like', $pattern)
          ->orWhere('mobilePhoneNumber', 'like', $pattern);
      });
    }

    if ($filter->withAllSubObjects) {
      $query->with('creator');
    }

    if ($orders && $orders->orders) {
      foreach ($orders->orders as $o) {
        $query->orderBy(str_replace('%app%', 'appointments', $o->field), $o->asc ? 'asc' : 'desc');
      }
    }

    if ($matchers) {
      /**
       * @var $m Matcher
       */
      foreach ($matchers as $m) {
        $op = 'like';
        $value = $m->value;
        if ($m->op == 'eq') {
          $op = '=';
        } else if ($m->op == 'gt') {
          $op = '>=';
        } else if ($m->op == 'atdate') {
          $op = 'like';
          $value = DateTimeUtils::dateOnlyToYMD($m->value) . ' %';
        }
        $query->where($m->field, $op, $value);
      }
    }

    $query->with('appointmentType');

    if (isset($filter->appointmentType) && $filter->appointmentType != null) {
      $query->whereHas('appointmentType', function ($query) use ($filter) {
        $query->where('name', '=', $filter->appointmentType);
      });
    }
    
    // $query->whereHas('appointmentType', function (Builder $query) {
    //   $query
    //     ->where('name', '<>', 'follow_up_appointment');
    // });

    return $query->get();

    //        echo $query->toSql();
    //        print_r($query->getBindings());
    //        die("OK");
  }

  /**
   * @param $locationId
   * @param $when
   * @param int $type
   * @return Appointment
   */
  public function createQuick($locationId, $when, $type = Appointment::TYPE_DEFAULT)
  {
    $app = new Appointment();
    $app->when = $when;
    $app->locationId = $locationId;
    $app->typ = $type;
    $app->createdUserId = 0;
    $app->save();
    return $app;
  }

  /**
   * @param $id
   * @return Appointment|null
   */
  public function findOriginalAppointmentOf($id)
  {
    /**
     * @var $app Appointment
     */
    $org = $this->byId($id);
    if ($org == null) return null;
    $pres = [];

    $app = $org;

    do {
      $app = $app->preAppointment;
      if ($app != null) {
        $pres[] = $app;
        //echo $app->id . "\r\n";
      }
    } while ($app != null);
    return count($pres) == 0 ? $org : $pres[count($pres) - 1];
  }

  public function purge(Appointment $app)
  {
    $app->delete();
  }

  /**
   * @param $userId
   * @return Builder[]|\Illuminate\Database\Eloquent\Collection
   */
  public function findAllCreatedByUser($userId)
  {
    return $this->query()->where('createdUserId', '=', $userId)->get();
  }

  public function purgeAllCreatedByUser($userId)
  {
    $apps = $this->findAllCreatedByUser($userId);
    foreach ($apps as $app) {
      $this->purge($app->id);
    }
  }

  public function permissionOfAppointmentInCompanyDetailBlock() {
    return [
      PermissionType::COMPANY_DETAILS_APPOINTMENTS_BLOCK_SHOW,
      PermissionType::COMPANY_DETAILS_APPOINTMENTS_BLOCK_ADD,
      PermissionType::COMPANY_DETAILS_APPOINTMENTS_BLOCK_EDIT,
    ];
   }
}
