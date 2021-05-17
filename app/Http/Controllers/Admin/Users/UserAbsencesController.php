<?php

/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Admin\Users;


use App\Entities\Core\UserAbsence;
use App\Http\Controllers\AbstractInternController;
use App\Http\Controllers\RestResultTrait;
use App\Services\Core\UserAbsenceService;
use App\Repositories\Core\UserAbsenceRepository;
use Illuminate\Http\Request;

class UserAbsencesController extends AbstractInternController
{

  private $userAbsenceRepository;
  private $userAbsenceService;

  public function __construct(
    UserAbsenceRepository $userAbsenceRepository,
    UserAbsenceService $userAbsenceService
  ) {
    $this->userAbsenceRepository = $userAbsenceRepository;
    $this->userAbsenceService = $userAbsenceService;
  }

  public function all()
  {
    $list = $this->userAbsenceRepository->all();
    return $this->singleJson($list);
  }

  public function allOfUser($userId)
  {
    $list = $this->userAbsenceRepository->allOfUser($userId);
    return $this->singleJson($list);
  }

  /**
   * @param int $id
   * @return void
   */

  public function singleUserAbsence($id)
  {
    $singleUserAbsence = $this->userAbsenceRepository->singleUserAbsence($id);
    return $this->singleJson($singleUserAbsence);
  }

  public function create(Request $request, $userId)
  {
    $a = new UserAbsence();
    $a->userId = $userId;
    $a->from = $request->get('from');
    $a->to = $request->get('to');
    $a->am = $request->get('am');
    $a->pm = $request->get('pm');
    $a->type_id = $request->get('type_id');
    $a->save();

    return $this->singleJson($a);
  }


  /**
   * @param Request $request
   * @param int $id
   * @return void
   */

   public function update(Request $request, $id)
  {
    $data = $request->all();
    $response =  $this->userAbsenceService->updateUserAbsences($data , $id);
    return $this->singleJson($response);
  }

  /**
   * deletes absence by id
   *
   * @param $userId
   * @param $id
   * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
   */
  public function deleteById($id)
  {
    $this->userAbsenceRepository->deleteById($id);
    return $this->noContent();
  }

  /**
   * deletes all absences of a user
   *
   * @param $userId
   * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
   */
  public function deleteAllOfUser($userId)
  {
    $userAbsences = $this->userAbsenceRepository->allOfUser($userId);

    foreach ($userAbsences as $userAbsence) {
      $this->userAbsenceRepository->deleteById($userAbsence->id);
    }

    return $this->noContent();
  }

  public function absenceTypes() {
    return $this->userAbsenceRepository->getAllAbsencesTypes();
  }
}
