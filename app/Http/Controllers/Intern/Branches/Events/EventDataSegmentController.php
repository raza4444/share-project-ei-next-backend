<?php

namespace App\Http\Controllers\Intern\Branches\Events;

use App\Entities\Branches\Events\EventDataSegment;
use App\Http\Controllers\AbstractInternController;

class EventDataSegmentController extends AbstractInternController
{
  public function __construct()
  {
  }

  public function all()
  {
    return EventDataSegment::with('values')->get();
  }
}
