<?php
/**
 * by stephan scheide
 */

namespace App\Services\Tasks;


use App\Entities\Branches\Appointment;
use App\Entities\Tasks\CounterTask;
use App\Entities\Tasks\CounterTaskEvent;
use App\Entities\Tasks\GenericTask;
use App\Entities\Tasks\GenericTaskTemplates;
use App\Repositories\Tasks\CounterTaskRepository;
use App\Utils\DateTimeUtils;

class BusinessCounterTaskEventService
{

    private $counterTaskRepository;

    public function __construct(CounterTaskRepository $counterTaskRepository)
    {
        $this->counterTaskRepository = $counterTaskRepository;
    }

    /**
     * Reagiere, wenn ein Termin erledigt wurde
     *
     * @param LocationEventAppointment $app
     */
    public function handleAfterAppointmentCreated(Appointment $app)
    {
        $task = $this->counterTaskRepository->findAndEnsureByName(CounterTaskRepository::TASK_KLICKER);
        $t = GenericTaskTemplates::KLICKER_CONTACT;

        //Erstelle Klicker.KontaktErstellen
        $event = $this->createNewSimpleCounterTaskEvent($task, $t['title'], $t['businessType']);
        $event->locationEventAppointmentId = $app->id;
        $event->dueAt = date('Y-m-d H:i:s', strtotime('-1 day', DateTimeUtils::timestampFromYMDHIS($app->when)));
        $event->save();

        //Erstelle Entwurf.EntwurfErstellen
        $task = $this->counterTaskRepository->findAndEnsureByName(CounterTaskRepository::TASK_ENTWURF);
        $t = GenericTaskTemplates::ENTWURF_ENTWURF_ERSTELLEN;
        $event = $this->createNewSimpleCounterTaskEvent($task, $t['title'], $t['businessType']);
        $event->locationEventAppointmentId = $app->id;
        $event->dueAt = date('Y-m-d H:i:s', strtotime('-1 day', DateTimeUtils::timestampFromYMDHIS($app->when)));
        $event->save();

    }

    public function handleCounterTaskEventDone(CounterTaskEvent $event)
    {
        /**
         * @var GenericTask $gt
         */
        $gt = $event->mainTask;

        if ($gt == null) {
            return;
        }

        //Wenn Klicker.Kontakt erledigt --> erstelle Klicker.Praesi
        if ($gt->businessType == GenericTaskTemplates::KLICKER_CONTACT['businessType']) {
            $t = GenericTaskTemplates::KLICKER_PRAESI;

            //Und wieder dem urspruegnlichem Termin zuweisen, so dass wir die Unternehmensdaten haben
            $createdEvent = $this->createNewSimpleCounterTaskEvent($event->counterTask, $t['title'], $t['businessType']);
            $createdEvent->locationEventAppointmentId = $event->locationEventAppointmentId;
            $createdEvent->dueAt = $event->dueAt;
            $createdEvent->save();
        }

        //Wenn Entwurf.Entwurferstellen erledigt --> Designkontrolli.EntwurfKontrollieren
        if ($gt->businessType == GenericTaskTemplates::ENTWURF_ENTWURF_ERSTELLEN['businessType']) {
            $counterTask = $this->counterTaskRepository->findAndEnsureByName(CounterTaskRepository::TASK_DESIGNKONTROLLE);
            $t = GenericTaskTemplates::DESIGNKONTROLLE_ENTWURF_KONTROLLIEREN;
            $createdEvent = $this->createNewSimpleCounterTaskEvent($counterTask, $t['title'], $t['businessType']);
            $createdEvent->locationEventAppointmentId = $event->locationEventAppointmentId;
            $createdEvent->dueAt = $event->dueAt;
            $createdEvent->save();
        }

    }

    /**
     * @param CounterTask $task
     * @param $title
     * @param $businessType
     * @return CounterTaskEvent
     */
    public function createNewSimpleCounterTaskEvent(CounterTask $task, $title, $businessType)
    {
        $gt = GenericTask::createSingleCheckActionTask($title, $businessType);
        $event = new CounterTaskEvent();
        $event->counterTaskId = $task->id;
        $event->mainTaskId = $gt->id;
        $event->save();
        return $event;
    }

}
