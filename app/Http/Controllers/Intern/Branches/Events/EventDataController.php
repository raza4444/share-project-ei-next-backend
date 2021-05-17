<?php

namespace App\Http\Controllers\Intern\Branches\Events;

use App\Entities\Branches\Events\EventData;
use App\Entities\Core\InternUser;
use App\Http\Controllers\AbstractInternController;
use Illuminate\Http\Request;

class EventDataController extends AbstractInternController
{
  private $eventDataNameController;
  private $eventDataGreetingNameController;
  private $eventDataMailController;
  private $eventDataNumberController;
  private $eventDataSubjectController;
  private $eventDataZipController;
  private $eventDataAddressController;
  // private $eventDataFollowUpCaptionController;
  private $eventDataSegmentValueController;

  public function __construct(
    EventDataNameController $eventDataNameController,
    EventDataGreetingNameController $eventDataGreetingNameController,
    EventDataMailController $eventDataMailController,
    EventDataNumberController $eventDataNumberController,
    EventDataSubjectController $eventDataSubjectController,
    EventDataZipController $eventDataZipController,
    EventDataAddressController $eventDataAddressController,
    EventDataFollowUpCaptionController $eventDataFollowUpCaptionController,
    EventDataSegmentValueController $eventDataSegmentValueController
  ) {
    $this->eventDataNameController = $eventDataNameController;
    $this->eventDataGreetingNameController = $eventDataGreetingNameController;
    $this->eventDataMailController = $eventDataMailController;
    $this->eventDataNumberController = $eventDataNumberController;
    $this->eventDataSubjectController = $eventDataSubjectController;
    $this->eventDataZipController = $eventDataZipController;
    $this->eventDataAddressController = $eventDataAddressController;
    $this->eventDataFollowUpCaptionController = $eventDataFollowUpCaptionController;
    $this->eventDataSegmentValueController = $eventDataSegmentValueController;
  }

  public function find(Request $request, $companyId)
  {

    // $queryParams = $this->validate($request, [
    //   'campaign_type' => ['required', 'regex:/^(whatsapp|kontaktformularlinks)$/']
    // ]);

    // $campaignType = $queryParams['campaign_type'];

    $eventDataObj = EventData::where('company_register_id', $companyId)
      ->with('eventDataName')
      ->with('eventDataGreetingName')
      ->with('eventDataMail')
      ->with('eventDataNumber')
      ->with('eventDataSubject')
      ->with('eventDataZip')
      ->with('eventDataAddress')
      // ->with('eventDataFollowUpCaption.segmentValues')
      ->with('eventDataSegmentValues')
      ->with('eventDataCurUser')
      ->first();

    $eventDataGreetingNameForMail = null;

    if ($eventDataObj) {

      $hasChanged = false;

      if (!$eventDataObj->eventDataMail) {
        $randomEventDataMail = $this->eventDataMailController->getRandomValue();
        if (isset($randomEventDataMail)) {
          $eventDataObj->event_data_mails_id = $randomEventDataMail->id;
          $hasChanged = true;
        }
      }

      /* Return 'Name' and 'Grussname' of same index as mail if mail is set
         If hasChanged = true, mail has changed so we need to update 'Name' and 'Grussname' as well */
      if (!$eventDataObj->eventDataName || $hasChanged) {
        if (isset($eventDataObj->event_data_mails_id)) {

          $eventDataNameForMail = $this->eventDataNameController->getForMail($eventDataObj->event_data_mails_id);
          if (isset($eventDataNameForMail)) {
            $eventDataObj->event_data_names_id = $eventDataNameForMail->id;
            $hasChanged = true;
          }

          $eventDataGreetingNameForMail = $this->eventDataGreetingNameController->getForMail($eventDataObj->event_data_mails_id);
          if (isset($eventDataGreetingNameForMail)) {
            $eventDataObj->event_data_greeting_names_id = $eventDataGreetingNameForMail->id;
            $hasChanged = true;
          }
        }
      }

      // Random return values

      if (!$eventDataObj->eventDataNumber && !$eventDataObj->event_data_custom_number) {
        $randomEventDataNumber = $this->eventDataNumberController->getRandomValue();
        if (isset($randomEventDataNumber)) {
          $eventDataObj->event_data_numbers_id = $randomEventDataNumber->id;
          $hasChanged = true;
        }
      }

      if (!$eventDataObj->eventDataSubject) {
        $randomEventDataSubject = $this->eventDataSubjectController->getRandomValue();
        if (isset($randomEventDataSubject)) {
          $eventDataObj->event_data_subjects_id = $randomEventDataSubject->id;
          $hasChanged = true;
        }
      }

      if (!$eventDataObj->eventDataZip) {
        $randomEventDataZip = $this->eventDataZipController->getRandomValue();
        if (isset($randomEventDataZip)) {
          $eventDataObj->event_data_zip_codes_id = $randomEventDataZip->id;
          $hasChanged = true;
        }
      }

      $randomEventDataAddress = null;
      if (!$eventDataObj->eventDataAddress) {
        $randomEventDataAddress = $this->eventDataAddressController->getRandomValue();
        if (isset($randomEventDataAddress)) {
          $eventDataObj->event_data_addresses_id = $randomEventDataAddress->id;
          $hasChanged = true;
        }
      }

      if (!$eventDataObj->eventDataCurUser) {
        if($request->get('user-id')) {
          $eventDataCurUser = InternUser::where('id', $request->get('user-id'))->first();
        } else {
          $eventDataCurUser = InternUser::where('id', $this->getCurrentUserId())->select(['id', 'end_greeting'])->first();
        }
        
        if (isset($eventDataCurUser)) {
          $eventDataObj->event_data_cur_user_id = $eventDataCurUser->id;
          $hasChanged = true;
        }
      }

      // if (!$eventDataObj->eventDataFollowUpCaption) {
      //   $randomEventDataFollowUpCaption = $this->eventDataFollowUpCaptionController->getNextValue($campaignType);
      //   if (isset($randomEventDataFollowUpCaption)) {
      //     $eventDataObj->event_data_follow_up_captions_id = $randomEventDataFollowUpCaption->id;
      //     $hasChanged = true;
      //   }
      // }

      // Save before handling segment values since they go to pivot table
      if ($hasChanged) {
        $eventDataObj->save();
      }

      if (!$eventDataObj->eventDataSegmentValues || sizeof($eventDataObj->eventDataSegmentValues) === 0) {
        $randomEventDataSegmentValues = $this->eventDataSegmentValueController->getRandomValuesPerCaption();
        if (isset($randomEventDataSegmentValues)) {
          $eventDataObj->eventDataSegmentValues()
            ->sync(array_map(function ($v) {
              return array_key_exists('id', $v) ? $v->id : null;
            }, $randomEventDataSegmentValues));
        }
        $hasChanged = true;
      }

      // Select again to receive updated with() values
      if ($hasChanged) {

        $eventDataObj = EventData::where('company_register_id', $companyId)
          ->with('eventDataName')
          ->with('eventDataGreetingName')
          ->with('eventDataMail')
          ->with('eventDataNumber')
          ->with('eventDataSubject')
          ->with('eventDataZip')
          ->with('eventDataAddress')
          // ->with('eventDataFollowUpCaption.segmentValues')
          ->with('eventDataSegmentValues')
          ->with('eventDataCurUser')
          ->first();
      }

      // Replace random number with custom number if set, before returning
      if ($eventDataObj->event_data_custom_number) {
        unset($eventDataObj->eventDataNumber->id);
        $eventDataObj->eventDataNumber->number = $eventDataObj->event_data_custom_number;
      }

      $eventDataDisplayObj = [
        'company_register_id' => $companyId,
        'event_data_name' => $eventDataObj->eventDataName,
        'event_data_greeting_name' => $eventDataObj->eventDataGreetingName,
        'event_data_mail' => $eventDataObj->eventDataMail,
        'event_data_number' => $eventDataObj->eventDataNumber,
        'event_data_subject' => $eventDataObj->eventDataSubject,
        'event_data_zip' => $eventDataObj->eventDataZip,
        'event_data_address' => $eventDataObj->eventDataAddress,
        // 'event_data_follow_up_caption' => $eventDataObj->eventDataFollowUpCaption
        'event_data_segment_values' => $eventDataObj->eventDataSegmentValues,
        'event_data_cur_user' => $eventDataObj->eventDataCurUser,
      ];
    } else { // No event data for given company register id, return object with random values

      $randomEventDataMail = $this->eventDataMailController->getRandomValue();
      $eventDataNameForMail = null;

      // Return 'Name' and 'Grussname' of same index as mail if mail is set
      if (isset($randomEventDataMail)) {
        $eventDataNameForMail = $this->eventDataNameController->getForMail($randomEventDataMail->id);
        $eventDataGreetingNameForMail = $this->eventDataGreetingNameController->getForMail($randomEventDataMail->id);
      }

      $randomEventDataNumber = $this->eventDataNumberController->getRandomValue();
      $randomEventDataSubject = $this->eventDataSubjectController->getRandomValue();
      $randomEventDataZip = $this->eventDataZipController->getRandomValue();
      $randomEventDataAddress = $this->eventDataAddressController->getRandomValue();
      // $randomEventDataFollowUpCaption = $this->eventDataFollowUpCaptionController->getNextValue($campaignType);
      $randomEventDataFollowUpSegments = $this->eventDataSegmentValueController->getRandomValuesPerCaption();
      
      if($request->get('user-id')) {
      $curUserObj = InternUser::where('id', $request->get('user-id'))->first();
      } else {
        $curUserObj = InternUser::where('id', $this->getCurrentUserId())->select(['id', 'end_greeting'])->first();
      } 
      $eventDataDisplayObj = [
        'company_register_id' => $companyId,
        'event_data_name' => $eventDataNameForMail ? $eventDataNameForMail : null,
        'event_data_greeting_name' => $eventDataGreetingNameForMail ? $eventDataGreetingNameForMail : null,
        'event_data_mail' => $randomEventDataMail ? $randomEventDataMail : null,
        'event_data_number' => $randomEventDataNumber ? $randomEventDataNumber : null,
        'event_data_subject' => $randomEventDataSubject ? $randomEventDataSubject : null,
        'event_data_zip' => $randomEventDataZip ? $randomEventDataZip : null,
        'event_data_address' => $randomEventDataAddress ? $randomEventDataAddress : null,
        // 'event_data_follow_up_caption' => $followUpCaption ? $followUpCaption : null,
        'event_data_segment_values' => $randomEventDataFollowUpSegments ? $randomEventDataFollowUpSegments : null,
        'event_data_cur_user' => $curUserObj ? $curUserObj : null
      ];

    }

    return $eventDataDisplayObj;
  }

  public function saveForCompanyRegisterId(Request $request, $companyId)
  {
    $eventDataObj = EventData::where('company_register_id', $companyId)->get();

    if (!$eventDataObj || !empty($eventDataObj)) {
      $eventDataObj = new EventData([
        'company_register_id' => $companyId
      ]);
    }

    $allParams = $request->all();

    if ($allParams['event_data_name_id']) {
      $eventDataObj->event_data_names_id = $allParams['event_data_name_id'];
    }

    if ($allParams['event_data_greeting_name_id']) {
      $eventDataObj->event_data_greeting_names_id = $allParams['event_data_greeting_name_id'];
    }

    if ($allParams['event_data_mail_id']) {
      $eventDataObj->event_data_mails_id = $allParams['event_data_mail_id'];
    }

    if ($allParams['event_data_number_id']) {
      $eventDataObj->event_data_numbers_id = $allParams['event_data_number_id'];
    }

    if ($allParams['event_data_custom_number']) {
      $eventDataObj->event_data_custom_number = $allParams['event_data_custom_number'];
    }

    if ($allParams['event_data_subject_id']) {
      $eventDataObj->event_data_subjects_id = $allParams['event_data_subject_id'];
    }

    if ($allParams['event_data_zip_id']) {
      $eventDataObj->event_data_zip_codes_id = $allParams['event_data_zip_id'];
    }

    if ($allParams['event_data_address_id']) {
      $eventDataObj->event_data_addresses_id = $allParams['event_data_address_id'];
    }

    // if ($allParams['event_data_follow_up_caption']) {
    //   $eventDataObj->event_data_follow_up_captions_id = isset($allParams['event_data_follow_up_caption']['id']) ? $allParams['event_data_follow_up_caption']['id'] : null;
    // }

    if ($allParams['event_data_cur_user_id']) {
      $eventDataObj->event_data_cur_user_id = $allParams['event_data_cur_user_id'];
    }

    $eventDataObj->save();

    // Sync segment values after saving, since now we have the id for the pivot table
    if ($allParams['event_data_segment_values']) {
      $eventDataObj->eventDataSegmentValues()->sync($allParams['event_data_segment_values']);
    }
  }
}
