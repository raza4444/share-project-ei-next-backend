<?php

/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Admin\Users;


use App\Entities\Core\VocationalSchool;
use App\Http\Controllers\AbstractInternController;
use App\Http\Controllers\RestResultTrait;
use App\Services\Core\VocationalSchoolService;
use App\Repositories\Core\VocationalSchoolRepository;


use Illuminate\Http\Request;

class VocationalSchoolController extends AbstractInternController
{

  private $vocationalSchoolService;
  private $vocationalSchoolRepository;

  public function __construct(
    VocationalSchoolService $vocationalSchoolService,
    VocationalSchoolRepository $vocationalSchoolRepository
  ) {
    $this->vocationalSchoolService = $vocationalSchoolService;
    $this->vocationalSchoolRepository = $vocationalSchoolRepository;
  }

  public function all()
  {
    $list = $this->vocationalSchoolRepository->all();
    return $this->singleJson($list);
  }

  /**
   * @param int $id
   * @return void
   */

  public function single($id)
  {
    $response = $this->vocationalSchoolRepository->byId($id);
    return $this->singleJson($response);
  }

  public function create(Request $request, $userId)
  {
    $a = new VocationalSchool();
    $a->userId = $userId;
    $a->info = $request->get('info');
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
    $response =  $this->vocationalSchoolService->update($data, $id);
    return $this->singleJson($response);
  }

  /**
   * @param $userId
   * @param $id
   */
  public function deleteById($id)
  {
    $this->vocationalSchoolRepository->deleteById($id);
    return $this->noContent();
  }
}
