<?php

namespace App\Services\Mail;

use App\Entities\Mail\MailTemplate;
use App\Repositories\Mail\MailTemplateRepository;

class MailTemplateService
{

  /**
   * @var mailTemplateRepository
   */
  private $mailTemplateRepository;

  /**
   * @param MailTemplateRepository $mailTemplateRepository
   */
  public function __construct( MailTemplateRepository $mailTemplateRepository ) {
    $this->mailTemplateRepository = $mailTemplateRepository;
  }

  public function create($data) {
    return $this->mailTemplateRepository->create($data);
  }

  public function getList()
  {
    return $this->mailTemplateRepository->getList();
  }

  public function byId($id)
  {
    return $this->mailTemplateRepository->byId($id);
  }

  public function update($data, $id)
  {
    $mailTemplate = $this->mailTemplateRepository->byId($id);

    if ($mailTemplate == null) {
      return null;
    }

    return $this->mailTemplateRepository->update($mailTemplate, $data);
  }

  public function delete($id)
  {
    $mailTemplate = $this->mailTemplateRepository->byId($id);

    if ($mailTemplate == null) {
      return null;
    }

    return $this->mailTemplateRepository->delete($mailTemplate);
  }

  public function getMailPlaceholders() {
   return $this->mailTemplateRepository->getMailPlaceholders();
  }
}
