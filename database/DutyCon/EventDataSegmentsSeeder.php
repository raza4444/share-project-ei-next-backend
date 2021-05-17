<?php

use App\Entities\Branches\Events\EventDataSegment;
use Illuminate\Database\Seeder;

class EventDataSegmentsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    EventDataSegment::create(array('running_number' => 1));
    EventDataSegment::create(array('running_number' => 2));
    EventDataSegment::create(array('running_number' => 3));
    EventDataSegment::create(array('running_number' => 4));
    EventDataSegment::create(array('running_number' => 5));
    EventDataSegment::create(array('running_number' => 6));
    EventDataSegment::create(array('running_number' => 7));
    EventDataSegment::create(array('running_number' => 8));
  }
}
