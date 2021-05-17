<?php

namespace App\Http\Controllers\Intern\Branches\Events;

use App\Entities\Branches\Events\EventDataAddress;
use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;

class EventDataAddressController extends AbstractInternController
{
  public function __construct()
  {
  }

  public function all()
  {
    return EventDataAddress::all();
  }

  public function create(Request $request)
  {
    $eventDataAddress = new EventDataAddress($request->all());
    $eventDataAddress->save();
    return $eventDataAddress;
  }

  public function update(Request $request, $id)
  {
    $eventDataAddress = EventDataAddress::findOrFail($id);
    $eventDataAddress['address'] = $request->all()['address'];
    $eventDataAddress->save();
    return EventDataAddress::find($id);
  }

  public function delete($id)
  {
    EventDataAddress::find($id)->delete();
    return $this->all();
  }

  public function getRandomValue()
  {
    $eventDataAddresses = EventDataAddress::all();
    if (!$eventDataAddresses->isEmpty()) {
      return $eventDataAddresses->random(1)[0];
    }
    return null;
  }
}
