<?php

/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Branches;

use App\Entities\Branches\Appointment;
use App\Http\Controllers\AbstractInternController;
use App\Http\Controllers\Intern\Duties\DutyRowController;
use App\Repositories\Branches\AppointmentRepository;
use App\Repositories\Branches\AppointmentTypeRepository;
use App\Repositories\Branches\BasicAppointmentFilter;
use App\Services\Branches\AppointmentService;
use App\ValueObjects\Core\FieldOrdersFactory;
use App\ValueObjects\Core\MatchersFactory;
use Illuminate\Http\Request;

class AppointmentController extends AbstractInternController
{

  private $appointmentRepository;
  private $appointmentTypeRepository;
  private $dutyRowController;

  private $appointmentService;

  public function __construct(
    AppointmentRepository $appointmentRepository,
    AppointmentTypeRepository $appointmentTypeRepository,
    AppointmentService $appointmentService,
    DutyRowController $dutyRowController
  ) {
    $this->appointmentRepository = $appointmentRepository;
    $this->appointmentTypeRepository = $appointmentTypeRepository;
    $this->appointmentService = $appointmentService;
    $this->dutyRowController = $dutyRowController;
  }

  public function create(Request $request, $locationId)
  {
    $req = $request->all();
    $app = new Appointment();
    $app->locationId = $locationId;
    $app->createdUserId = $this->getCurrentUserId();
    $app->when = $req[0]['when'];
    $app->appointmentTypeId = $req[0]['appointmentTypeId'];
    $app->save();

    // Generate duty rows for new appointment (req[1] = callerRowId)
    $this->dutyRowController->createRowsAndTasksForNewAppointment($app->id, $locationId, isset($req[1]) ? $req[1] : null);

    return $this->singleJson($app);
  }

  public function findTypes()
  {
    return $this->singleJson($this->appointmentTypeRepository->all());
  }

  public function findFiltered(Request $request)
  {

    $filter = new BasicAppointmentFilter();

    if ($request->has('allsubobjects') && $request->get('allsubobjects') == 1) {
      $filter->withAllSubObjects = true;
    }
    if ($request->has('year')) {
      $filter->year = $request->get('year') * 1;
    }
    if ($request->has('years')) {
      $filter->years = explode(',', $request->get('years'));
    }
    if ($request->has('whenyears')) {
      $filter->whenyears = explode(',', $request->get('whenyears'));
    }
    if ($request->has('werbeaktion')) {
      $filter->werbeaktion = $request->get('werbeaktion');
    }
    if ($request->has('empty-result') && $request->get('empty-result') == 1) {
      $filter->onlyEmptyResult = true;
      $filter->withAllSubObjects = true;
    }
    if ($request->has('gone') && $request->get('gone') == 1) {
      $filter->onlyGone = true;
      $filter->withAllSubObjects = true;
    }
    if ($request->has('upcoming') && $request->get('upcoming') == 1) {
      $filter->onlyUpcoming = true;
      $filter->withAllSubObjects = true;
    }
    if ($request->has('skip')) {
      $filter->skip = $request->get('skip') * 1;
    }

    if ($request->has('top')) {
      $filter->top = $request->get('top') * 1;
    }
    if ($request->has('search')) {
      $filter->search = $request->get('search');
    }
    if ($request->has('appointmentType')) {
      $filter->appointmentType = $request->get('appointmentType');
    }

    $orders = FieldOrdersFactory::byRequest($request);
    $matchers = MatchersFactory::byRequest($request);

    return $this->singleJson($this->appointmentRepository->findFiltered($filter, $orders, $matchers));
  }

  public function updatePartial(Request $request, $id)
  {
    //$userId = $this->getCurrentUserId();

    /**
     * @var $app Appointment
     */
    $app = $this->appointmentRepository->byId($id);
    if ($app == null) {
      return $this->notFound();
    }

    $changeAbleKeys = ['erinnernAm', 'nachgehenAm', 'status', 'ansprechpartner_anrede', 'ansprechpartner_vorname', 'ansprechpartner_nachname'];
    $all = $request->all();
    $changed = false;
    foreach ($changeAbleKeys as $key) {
      if (array_key_exists($key, $all)) {
        $app->$key = $all[$key];
        $changed = true;
      }
    }

    if ($changed) {
      $app->save();
    }

    return $this->singleJson($this->appointmentRepository->byId($id));
  }

  public function updateResult(Request $request, $id)
  {
    /**
     * @var Appointment $app
     */
    $app = $this->appointmentRepository->byIdActive($id);

    //Termin muss existieren
    if ($app == null) {
      return $this->notFound();
    }

    //Ergebnis bereits gesetzt?
    if (!$app->canChangeResult()) {
      return $this->badRequest();
    }

    $body = $request->all();
    $result = $body[0]['result'] * 1;
    $seller = isset($body[0]['seller']) ? $body[0]['seller'] : null;

    //Verkauf gesondert behandeln
    if ($result == Appointment::RESULT_VERKAUFT) {
      return $this->singleJson($this->updateResultToSold($id, $seller)->toArray());
    }

    $userId = $this->getCurrentUserId();
    $app->result = $result;

    $app->finished_at = date('Y-m-d H:i:s');
    $app->finishedUserId = $userId;
    $app->seller = $seller;

    if (isset($body[0]['newAppointmentAt']) && $app->result * 1 == 20) {
      $newApp = new Appointment();
      $newApp->appointmentTypeId = $app->appointmentTypeId;
      $newApp->erinnernAm = $app->erinnernAm;
      $newApp->nachgehenAm = $app->nachgehenAm;
      $newApp->ansprechpartner_nachname = $app->ansprechpartner_nachname;
      $newApp->ansprechpartner_vorname = $app->ansprechpartner_vorname;
      $newApp->ansprechpartner_anrede = $app->ansprechpartner_anrede;
      $newApp->createdUserId = $userId;
      $newApp->locationId = $app->locationId;
      $newApp->eventId = $app->eventId;
      $newApp->preAppointmentId = $app->id;
      $newApp->when = $body[0]['newAppointmentAt'];
      $newApp->save();
      $app->nextAppointmentId = $newApp->id;

      // $this->dutyRowController->updateAppointmentInRows($app->id, $app->nextAppointmentId);

      // Generate duty rows for new appointment (req[1] = callerRowId)
      $this->dutyRowController->createRowsAndTasksForNewAppointment($app->nextAppointmentId, $app->locationId, isset($body[1]) ? $body[1] : null);
    }

    $app->save();

    return $this->singleJson($app->toArray());
  }

  public function byId($id)
  {
    $app = $this->appointmentRepository
      ->getQuery()
      ->where('id', '=', $id)
      ->with('creator')
      ->with('event')
      ->with('event.notes')
      ->with('event.notes.user')
      ->with('location')
      ->with('location.notes')
      ->with('location.notes.user')
      ->first();

    if ($app == null) {
      return $this->notFound();
    }

    return $this->singleJson($app);
  }

  public function findByDay($ymd)
  {
    $apps = $this->appointmentRepository->findOfDay($ymd, null, null);
    return $this->json(200, $apps->toArray());
  }

  public function findByDayAndType($ymd, $appointmentTypeId)
  {
    $apps = $this->appointmentRepository->findOfDay($ymd, $appointmentTypeId, 0);
    return $this->json(200, $apps->toArray());
  }

  /**
   * Markiert einen Termin als "Verkauft"
   * @param $id
   * @param $seller
   * @return Appointment
   */
  private function updateResultToSold($id, $seller)
  {
    return $this->appointmentService->markAsSoldNow($id, $this->getCurrentUserId(), $seller);
  }

  /**
   * 
   * Finds all appointments for a specific location
   * 
   * @param $locationId the id of the location
   * 
   * @return Appointment[]
   * 
   */
  public function findAppointmentsForLocationId($locationId)
  {
    return $this->singleJson($this->appointmentRepository->findAllOfLocation($locationId));
  }
}
