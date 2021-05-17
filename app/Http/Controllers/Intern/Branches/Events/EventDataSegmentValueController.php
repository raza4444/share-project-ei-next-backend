<?php

namespace App\Http\Controllers\Intern\Branches\Events;

use App\Entities\Branches\Events\EventDataSegmentValue;
use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventDataSegmentValueController extends AbstractInternController
{
  public function __construct()
  {
  }

  public function create(Request $request, $id)
  {
    $eventDataSegmentValue = new EventDataSegmentValue($request->all());
    $eventDataSegmentValue->segment_id = $id;
    $eventDataSegmentValue->save();
    return $eventDataSegmentValue;
  }

  public function update(Request $request, $id)
  {
    $eventDataSegmentValue = EventDataSegmentValue::findOrFail($id);
    $eventDataSegmentValue['value'] = $request->all()['value'];
    $eventDataSegmentValue->save();
    return EventDataSegmentValue::find($id);
  }

  public function delete($id)
  {
    $eventDataSegmentValue = EventDataSegmentValue::find($id);
    $eventDataSegmentValue->delete();
    return EventDataSegmentValue::where('segment_id', $eventDataSegmentValue->segment_id)->get();
  }

  public function getRandomValuesPerCaption()
  {
    return DB::select('SELECT id, segment_id, value FROM (SELECT id, segment_id, value FROM event_data_segment_values order by rand()) AS _SUB GROUP BY _SUB.segment_id');
  }
}
