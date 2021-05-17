<?php

namespace App\Http\Controllers\Intern\Branches\Events;

use App\Entities\Branches\Events\EventDataMail;
use App\Entities\Branches\Events\EventDataName;
use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;

class EventDataNameController extends AbstractInternController
{
  public function __construct()
  {
  }

  public function all()
  {
    return EventDataName::all();
  }

  public function create(Request $request)
  {
    $eventDataName = new EventDataName($request->all());
    $eventDataName->save();
    return $eventDataName;
  }

  public function update(Request $request, $id)
  {
    $eventDataName = EventDataName::findOrFail($id);
    $eventDataName['name'] = $request->all()['name'];
    $eventDataName->save();
    return EventDataName::find($id);
  }

  public function delete($id)
  {
    EventDataName::find($id)->delete();
    return $this->all();
  }

  public function getRandomValue()
  {
    $eventNames = EventDataName::all();
    if (!$eventNames->isEmpty()) {
      return $eventNames->random(1)[0];
    }
    return null;
  }

  /**
   * Returns name at same index as given mail or null if not set
   */
  public function getForMail($mailId)
  {
    $eventMails = EventDataMail::all();
    $eventNames = EventDataName::all();

    for ($i = 0; $i < sizeof($eventMails); $i++) {
      if ($eventMails[$i]->id === $mailId) {
        if (isset($eventNames[$i])) {
          return $eventNames[$i];
        }
        return null;
      }
    }
  }
}
