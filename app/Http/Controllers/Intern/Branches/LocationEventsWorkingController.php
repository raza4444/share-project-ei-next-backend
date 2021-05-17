<?php

/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Intern\Branches;


use App\Entities\Branches\LocationEvent;
use App\Entities\Branches\LocationEventTrack;
use App\Http\Controllers\AbstractInternController;
use App\Logging\AppLogger;
use App\Repositories\Branches\LocationEventRepository;
use App\Servers\EventServerClient;
use App\Services\Branches\LocationDeletionService;
use App\Services\Branches\LocationEventInterestService;
use App\Services\Branches\LocationEventLockingService;
use App\Services\Branches\LocationEventsToBeDoneService;
use App\Services\Branches\LocationEventTrackingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LocationEventsWorkingController extends AbstractInternController
{

  private $locationEventRepository;

  private $locationEventLockingService;

  private $locationEventsToBeDoneService;

  private $locationEventTrackingService;

  private $locationDeletionService;

  private $locationEventInterestService;

  public function __construct(
    LocationEventRepository $locationEventRepository,
    LocationEventsToBeDoneService $locationEventsToBeDoneService,
    LocationEventLockingService $locationEventLockingService,
    LocationDeletionService $locationDeletionService,
    LocationEventInterestService $locationEventInterestService,
    LocationEventTrackingService $locationEventTrackingService
  ) {
    $this->locationEventRepository = $locationEventRepository;
    $this->locationEventLockingService = $locationEventLockingService;
    $this->locationEventsToBeDoneService = $locationEventsToBeDoneService;
    $this->locationEventTrackingService = $locationEventTrackingService;
    $this->locationDeletionService = $locationDeletionService;
    $this->locationEventInterestService = $locationEventInterestService;
  }

  public function countToDo()
  {
    if (!$this->locationEventLockingService->isLockingSystemAvailable()) {
      return $this->ourResponse(null, 503);
    }
    $cc = $this->locationEventsToBeDoneService->countToDo();
    return $this->singleJson(['count' => $cc]);
  }

  // public function nextMockEventForWork()
  // {
  //   return '{"id":58567,"lockedUserId":null,"ursprungseventId":null,"showAfter":"2019-06-27 21:26:43","timestamp":"2019-06-27 21:26:43","showAfterTriedCount":1,"agentId":null,"agentLastSeen":null,"done":0,"failed":0,"result":null,"created_at":"2019-06-27 21:26:43","updated_at":"2019-06-27 21:26:43","schoolId":127048,"agentFromId":null,"agentLastChangeId":null,"deleted_at":null,"revision":null,"finishedTimestamp":null,"finishedAgentId":null,"note":null,"markierung":null,"notiz":null,"arbeitskategorie":0,"wiedervorlage":0,"shownAt":null,"ansprechpartner":null,"erlaubnis_anrufen":0,"anzahl_wiedervorlagen":0,"location":{"id":127048,"created_at":"2019-06-27 19:30:41","updated_at":"2019-06-27 19:30:41","title":"Schnittstelle Hair& Style, Inh. Dirkes, Ann-Christin","street":"Grader Weg 41","zip":"26871","country":"Deutschland","bundeslandid":11,"phoneNumber":"+4949619823980","mobilePhoneNumber":null,"email":null,"fax":null,"homepage":null,"notice":null,"city":"Papenburg","agentFromId":null,"agentLastChangeId":null,"deleted_at":null,"revision":null,"locationCategoryId":22,"markierung":null,"mondayId":null,"werbeaktion":null,"manuellErstellt":0,"said_whatsapp":null,"ist_alte_homepage":0,"homepage_info":null,"customerstate":0,"canlogin":1,"username":"K127048","password":null,"registerlink":null,"domain":null,"ftphost":null,"ftpusername":null,"ftppassword":null,"ftpdirectoryhtml":null,"ansprechpartner_anrede":null,"ansprechpartner_vorname":null,"ansprechpartner_nachname":null,"wiederkontaktAm":null,"location_category":{"id":22,"title":"friseursalons","created_at":"2019-06-27 18:21:43","updated_at":"2019-06-27 18:21:43","deleted_at":null,"agentFromId":null,"agentLastChangeId":null,"revision":null,"stacktraceLastChange":null}},"notes":[],"tracks":[{"id":232397,"eventId":58567,"userId":1,"trackedAt":"2019-12-19 12:43:34","action":"opened","result":null,"notice":null,"created_at":"2019-12-19 12:43:34","updated_at":"2019-12-19 12:43:34","appointmentAt":null,"showAgainAt":null}]}';
  // }

  public function nextEventForWork()
  {
    $uid = $this->getCurrentUserId();

    //ID bestimmen
    $id = $this->locationEventLockingService->findNextFreeEventIdAndLockForUser($uid);
    //$id = 166;

    if ($id === null) {
      return $this->notFound();
    }

    $event = $this->locationEventRepository->findForWorkById($id);
    if ($event === null) {
      return $this->notFound();
    }

    $this->locationEventTrackingService->trackEventOpen($id, $uid);

    //Kategorie mitladen
    if ($event && $event->location) {
      $event->location->load('locationCategory');
    }

    //X. Wiedervorlage
    $wv = 0;
    $fines = Carbon::now()->subMonth(3);
    foreach ($event->tracks as $track) {
      /**
       * @var LocationEventTrack $track
       * @var Carbon $ca
       */

      //Wiedervorlagenzaehlung auf 3 Monate begrenzen
      $ca = $track->created_at;
      if ($ca->lt($fines)) {
        continue;
      }

      if ($track->result == 'showAgain') {
        $wv++;
      }
    }

    $event->anzahl_wiedervorlagen = $wv;

    return $this->singleJson($event->toArray());
  }

  public function saveResult(Request $request)
  {

    $log = new AppLogger('work-save-result');

    $to = $request->all();
    $eventId = $to['eventId'];

    /**
     * @var $event LocationEvent
     */
    $event = $this->locationEventRepository->byId($eventId);
    if ($event == null) {
      return $this->notFound();
    }

    $done = 1;

    //Fuer Historie und Statistik gesondert speichern
    if ($to['result'] == 'showAgain') {
      $this->locationEventTrackingService->trackResultSavedShowAgain($eventId, $this->getCurrentUserId(), $to['showAgainAt']);
      $event->wiedervorlage = 1;
      $done = 0;
    } else {
      $this->locationEventTrackingService->trackResultSaved(
        $eventId,
        $this->getCurrentUserId(),
        $to['result'],
        $to['notice'],
        array_key_exists('appointmentAt', $to) ? $to['appointmentAt'] : null,
        array_key_exists('appointmentTypeId', $to) ? $to['appointmentTypeId'] : null
      );
      $event->wiedervorlage = 0;
    }

    //Ergebnis speichern
    //Vorsicht, Wiedervorlage beruecksichtigen, darf das Ereignis nicht auf erledigt setzen
    $event->result = $to['result'];
    $event->done = $done;
    $event->finishedTimestamp = $done == 1 ? Carbon::now() : null;
    $event->notiz = $to['notice'];
    $event->lockedUserId = null;
    $event->agentLastChangeId = $this->getCurrentUserId();
    $event->ansprechpartner = $to['ansprechpartner'];
    $event->erlaubnis_anrufen = $to['erlaubnis_anrufen'] * 1;

    if ($to['result'] == 'showAgain') {
      $event->showAfter = $to['showAgainAt'];
      $log->debug(['show after', $event->showAfter]);
    }
    $event->save();

    //Nachricht an Sperrserver, damit Wiedervorlage-Ereignis erneut vergeben wird
    if ($to['result'] == 'showAgain') {
      $client = new EventServerClient();
      $client->markEventAsUseAbleAgain($event->id);
    }

    $event->addAndSaveNoticeIfNotEmpty($this->getCurrentUser(), $to['noticeGeneral']);

    if ($to['result'] == 'notUsable') {
      $this->locationDeletionService->deleteLocationAndEventsLogically($event->location->id);
    } else if ($to['result'] == 'interest') {
      $this->locationEventInterestService->handleInterestByEventId($event->id, $this->getCurrentUserId(), $to);
    }

    return $this->singleJson($event, 200);
  }
}
