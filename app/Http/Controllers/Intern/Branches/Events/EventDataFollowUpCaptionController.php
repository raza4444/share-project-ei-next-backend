<?php

namespace App\Http\Controllers\Intern\Branches\Events;

use App\Entities\Branches\Events\EventDataFollowUpCaption;
use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;

class EventDataFollowUpCaptionController extends AbstractInternController
{
  public function __construct()
  {
  }

  public function all()
  {
    return EventDataFollowUpCaption::orderBy('order', 'asc')->get();
  }

  public function allWithSegmentValues()
  {
    return EventDataFollowUpCaption::orderBy('order', 'asc')->with('segmentValues')->get();
  }

  public function create(Request $request)
  {
    $eventDataFollowUpCaption = new EventDataFollowUpCaption($request->all());
    $eventDataFollowUpCaption->save();
    return $eventDataFollowUpCaption;
  }

  public function update(Request $request, $id)
  {
    $eventDataFollowUpCaption = EventDataFollowUpCaption::findOrFail($id);
    $eventDataFollowUpCaption['caption'] = $request->all()['caption'];
    $eventDataFollowUpCaption['campaign_type'] = $request->all()['campaign_type'];
    $eventDataFollowUpCaption['order'] = $request->all()['order'];

    // Segment Values
    $syncData = array();

    foreach ($request->all()['segment_values'] as $segmentValue) {
      $syncData[$segmentValue['id']] = ['order' => $segmentValue['pivot']['order']];
    }

    $eventDataFollowUpCaption->segmentValues()->sync($syncData);

    $eventDataFollowUpCaption->save();
    return EventDataFollowUpCaption::where('id', $id)->orderBy('order', 'asc')->with('segmentValues')->first();
  }

  public function delete($id)
  {
    EventDataFollowUpCaption::find($id)->delete();
    return $this->noContent();
  }

  public function getNextValue($campaignType)
  {
    $eventFollowUpCaptions = EventDataFollowUpCaption::where('campaign_type', $campaignType)
      ->with(['segmentValues' => function ($q) {
        $q->orderBy('pivot_order', 'asc');
      }])->get();

    if (!empty($eventFollowUpCaptions)) {

      for ($i = 0; $i < sizeof($eventFollowUpCaptions); $i++) {

        if ($eventFollowUpCaptions[$i]->last_shown === 1) {

          if ($i === sizeof($eventFollowUpCaptions) - 1) { // last, take first

            $this->updateLastShown($eventFollowUpCaptions[0]->id, $eventFollowUpCaptions[$i]->id);
            return $eventFollowUpCaptions[0];
          } else {
            // not last, take next
            $this->updateLastShown($eventFollowUpCaptions[$i + 1]->id, $eventFollowUpCaptions[$i]->id);
            return $eventFollowUpCaptions[$i + 1];
          }
        }
      }

      // no shown caption yet
      $this->updateLastShown($eventFollowUpCaptions[0]->id, null);
      return $eventFollowUpCaptions[0];
    }

    return null;
  }

  private function updateLastShown($nextShownId, $lastShownId)
  {
    if ($lastShownId) {
      $lastShownCaption = EventDataFollowUpCaption::find($lastShownId);
      if ($lastShownCaption) {
        $lastShownCaption->last_shown = null;
        $lastShownCaption->save();
      }
    }

    $nextShownCaption = EventDataFollowUpCaption::find($nextShownId);
    if ($nextShownCaption) {
      $nextShownCaption->last_shown = 1;
      $nextShownCaption->save();
    }
  }
}
