<?php

namespace App\Http\Controllers\Intern\Branches\Events;

use App\Entities\Branches\Events\EventDataSubject;
use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;

class EventDataSubjectController extends AbstractInternController
{
  public function __construct()
  {
  }

  public function all()
  {
    return EventDataSubject::all();
  }

  public function create(Request $request)
  {
    $eventDataSubject = new EventDataSubject($request->all());
    $eventDataSubject->save();
    return $eventDataSubject;
  }

  public function update(Request $request, $id)
  {
    $eventDataSubject = EventDataSubject::findOrFail($id);
    $eventDataSubject['subject'] = $request->all()['subject'];
    $eventDataSubject->save();
    return EventDataSubject::find($id);
  }

  public function delete($id)
  {
    EventDataSubject::find($id)->delete();
    return $this->all();
  }

  public function getRandomValue()
  {
    $eventDataSubjects = EventDataSubject::all();
    if (!$eventDataSubjects->isEmpty()) {
      return $eventDataSubjects->random(1)[0];
    }
    return null;
  }
}
