<?php

use App\Entities\Branches\Events\EventDataSegmentValue;
use Illuminate\Database\Seeder;

class EventDataSegmentValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      EventDataSegmentValue::create(array(
        'segment_id' => 1,
        'value' => 'Folgetext1'
      ));
      
      EventDataSegmentValue::create(array(
        'segment_id' => 2,
        'value' => 'Folgetext2'
      ));

      EventDataSegmentValue::create(array(
        'segment_id' => 3,
        'value' => 'Folgetext3'
      ));

      EventDataSegmentValue::create(array(
        'segment_id' => 4,
        'value' => 'Folgetext4'
      ));

      EventDataSegmentValue::create(array(
        'segment_id' => 5,
        'value' => 'Folgetext5'
      ));

      EventDataSegmentValue::create(array(
        'segment_id' => 6,
        'value' => 'Folgetext6'
      ));

      EventDataSegmentValue::create(array(
        'segment_id' => 7,
        'value' => 'Folgetext7'
      ));

      EventDataSegmentValue::create(array(
        'segment_id' => 8,
        'value' => 'Folgetext8'
      ));
    }
}
