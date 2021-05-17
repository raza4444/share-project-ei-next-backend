<?php

namespace App\Http\Controllers\Intern\Branches\Events;

use App\Entities\Branches\Events\EventDataMail;
use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;

class EventDataMailController extends AbstractInternController
{
  public function __construct()
  {
  }

  public function all()
  {
    return EventDataMail::all();
  }

  public function create(Request $request)
  {
    $eventDataMail = new EventDataMail($request->all());
    $eventDataMail->save();
    return $eventDataMail;
  }

  public function update(Request $request, $id)
  {
    $eventDataMail = EventDataMail::findOrFail($id);
    $eventDataMail['mail'] = $request->all()['mail'];
    $eventDataMail['pw_info'] = $request->all()['pw_info'];
    $eventDataMail->save();
    return EventDataMail::find($id);
  }

  public function delete($id)
  {
    EventDataMail::find($id)->delete();
    return $this->all();
  }

  public function getRandomValue()
  {
    $eventMails = EventDataMail::all();
    if (!$eventMails->isEmpty()) {
      return $eventMails->random(1)[0];
    }
    return null;
  }
}
