<?php

namespace App\Http\Controllers\Intern\Branches\Events;

use App\Entities\Branches\Events\EventDataGreetingName;
use App\Entities\Branches\Events\EventDataMail;
use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;

class EventDataGreetingNameController extends AbstractInternController
{
  public function __construct()
  {
  }

  public function all()
  {
    return EventDataGreetingName::all();
  }

  public function create(Request $request)
  {
    $eventDataGreetingName = new EventDataGreetingName($request->all());
    $eventDataGreetingName->save();
    return $eventDataGreetingName;
  }

  public function update(Request $request, $id)
  {
    $eventDataGreetingName = EventDataGreetingName::findOrFail($id);
    $eventDataGreetingName['greeting_name'] = $request->all()['greeting_name'];
    $eventDataGreetingName->save();
    return EventDataGreetingName::find($id);
  }

  public function delete($id)
  {
    EventDataGreetingName::find($id)->delete();
    return $this->all();
  }

  /**
   * Returns greeting name at same index as given mail or null if not set
   */
  public function getForMail($mailId)
  {
    $eventMails = EventDataMail::all();
    $eventGreetingNames = EventDataGreetingName::all();

    for ($i = 0; $i < sizeof($eventMails); $i++) {
      if ($eventMails[$i]->id === $mailId) {
        if (isset($eventGreetingNames[$i])) {
          return $eventGreetingNames[$i];
        }
        return null;
      }
    }
  }
}
