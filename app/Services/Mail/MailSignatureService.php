<?php

namespace App\Services\Mail;

use App\Repositories\Mail\MailSignatureRepository;

class MailSignatureService
{

  /**
   * @var mailSignatureRepository
   */
  private $mailSignatureRepository;

  /**
   * @param CrawlerRepository $crawlerRepository
   */
  public function __construct(
    MailSignatureRepository $mailSignatureRepository
  ) {
    $this->mailSignatureRepository = $mailSignatureRepository;
  }

  public function create($data) {
    return $this->mailSignatureRepository->create($data);
  }

  public function getList()
  {
    return $this->mailSignatureRepository->getList();
  }

  public function byId($id)
  {
    return $this->mailSignatureRepository->byId($id);
  }

  public function update($data, $id)
  {
    $mailSignature = $this->mailSignatureRepository->byId($id);

    if ($mailSignature == null) {
      return null;
    }

    return $this->mailSignatureRepository->update($mailSignature, $data);
  }

  public function delete($id)
  {
    $mailSignature = $this->mailSignatureRepository->byId($id);

    if ($mailSignature == null) {
      return null;
    }

    return $this->mailSignatureRepository->delete($mailSignature);
  }

  /**
   * @param string $name
   * @return boolean
   */
  public function isNameExist($name) {
   return $this->mailSignatureRepository->isNameExist($name);
  }

}
