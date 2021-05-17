<?php

namespace App\Http\Controllers\Intern\Branches\Events;

use App\Entities\Branches\Events\EventDataNumber;
use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;

class EventDataNumberController extends AbstractInternController
{
  public function __construct()
  {
  }

  public function all()
  {
    return EventDataNumber::all();
  }

  public function create(Request $request)
  {
    $eventDataNumber = new EventDataNumber($request->all());
    $eventDataNumber->save();
    return $eventDataNumber;
  }

  public function update(Request $request, $id)
  {
    $eventDataNumber = EventDataNumber::findOrFail($id);
    $eventDataNumber['number'] = $request->all()['number'];
    $eventDataNumber->save();
    return EventDataNumber::find($id);
  }

  public function delete($id)
  {
    EventDataNumber::find($id)->delete();
    return $this->all();
  }

  public function getRandomValue()
  {
    $eventNumbers = EventDataNumber::all();
    if (!$eventNumbers->isEmpty()) {
      return $eventNumbers->random(1)[0];
    }
    return null;
  }
}
