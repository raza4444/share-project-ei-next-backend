<?php

namespace App\Services\Branches;

use App\Entities\Branches\LocationNote;
use App\Repositories\Branches\LocationMailRepository;
use App\Repositories\Branches\LocationNoteRepository;

class LocationMailService
{

  /**
   * @var locationMailRepository
   */
  private $locationMailRepository;

  /**
   * @var locationService
   */
  private $locationService;

  /**
   * @var locationNoteRepository
   */
  private $locationNoteRepository;
  /**
   * @param LocationMailRepository $locationMailRepository
   */
  public function __construct( LocationMailRepository $locationMailRepository, LocationService $locationService, LocationNoteRepository $locationNoteRepository ) {
    $this->locationMailRepository = $locationMailRepository;
    $this->locationService = $locationService;
    $this->locationNoteRepository = $locationNoteRepository;
  }

  public function create($data) {
    return $this->locationMailRepository->create($data);
  }

  public function createBySender($data) {
    if ($this->locationMailRepository->byMessageId($data['message_id'])) {
      return;
    }

    $sentMail = $this->locationMailRepository->findByToEmail($data['from']);
    $locationId = null;

    if ($sentMail) {
      $locationId = $sentMail['location_id'];
    } else {
      $location = $this->locationService->findByEmail($data['from']);
      if ($location) {
        $locationId = $location['id'];
      } else {
        return;
      }
    }

    if ($locationId) {
      $data['location_id'] = $locationId;
      $mail = $this->locationMailRepository->create($data);
      $notesCount = $this->locationNoteRepository->countAll($locationId);
      $locationNoteData = [
        "locationId" => $data['location_id'],
        "userId" => null,
        "title" => null,
        "content" => null,
        "posX" => 1,
        "posY" => $notesCount,
        "pinned" => false,
        "location_mail_id" => $mail['id']
      ];

      $locationNote = new LocationNote($locationNoteData);
      $locationNote->save();
    }
  }

  public function getListByLocationId($locationId)
  {
    return $this->locationMailRepository->getListByLocationId($locationId);
  }

  public function byId($id)
  {
    return $this->locationMailRepository->byId($id);
  }

  public function delete($id)
  {
    $mailTemplate = $this->locationMailRepository->byId($id);

    if ($mailTemplate == null) {
      return null;
    }

    return $this->locationMailRepository->delete($mailTemplate);
  }

  public function getLastIncomingEmailDate()
  {
    return $this->locationMailRepository->getLastIncomingEmailDate();
  }
}
