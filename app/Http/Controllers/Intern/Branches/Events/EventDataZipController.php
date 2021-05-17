<?php

namespace App\Http\Controllers\Intern\Branches\Events;

use App\Entities\Branches\Events\EventDataZip;
use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;

class EventDataZipController extends AbstractInternController
{
  public function __construct()
  {
  }

  public function all()
  {
    return EventDataZip::all();
  }

  public function create(Request $request)
  {
    $eventDataZip = new EventDataZip($request->all());
    $eventDataZip->save();
    return $eventDataZip;
  }

  public function update(Request $request, $id)
  {
    $eventDataZip = EventDataZip::findOrFail($id);
    $eventDataZip['zip'] = $request->all()['zip'];
    $eventDataZip->save();
    return EventDataZip::find($id);
  }

  public function delete($id)
  {
    EventDataZip::find($id)->delete();
    return $this->all();
  }

  public function getRandomValue()
  {
    $eventDataZipCodes = EventDataZip::all();
    if (!$eventDataZipCodes->isEmpty()) {
      return $eventDataZipCodes->random(1)[0];
    }
    return null;
  }
}
