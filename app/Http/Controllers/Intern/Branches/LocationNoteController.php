<?php

namespace App\Http\Controllers\Intern\Branches;

use App\Entities\Branches\LocationNote;
use App\Entities\Branches\LocationNoteComment;
use App\Http\Controllers\AbstractInternController;
use App\Repositories\Branches\LocationNoteRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LocationNoteController extends AbstractInternController
{

  private $locationNoteRepository;

  /**
   * LocationNoteController constructor.
   * @param LocationNoteRepository $locationNoteRepository
   */
  public function __construct(LocationNoteRepository $locationNoteRepository) {
    $this->locationNoteRepository = $locationNoteRepository;
  }

  /**
   * returns all notes for a specific location
   *
   * @param $locationId
   * @return JsonResponse
   */
  public function findAll($locationId)
  {
    return $this->singleJson($this->locationNoteRepository->byLocationId($locationId));
  }

  /**
   * returns the number of notes for a specific location
   *
   * @param $locationId
   * @return JsonResponse
   */
  public function countAll($locationId)
  {
    return $this->singleJson($this->locationNoteRepository->countAll($locationId));
  }

  /**
   * updates a specific location note
   *
   * @param $request the http request
   * @param $id
   * @return JsonResponse
   */
  public function update(Request $request, $id)
  {
    return $this->singleJson($this->locationNoteRepository->update($id, $request->all()));
  }

  /**
   * updates given notes after reordering
   *
   * @param $request the http request
   * @return JsonResponse
   */
  public function reorder(Request $request)
  {
    $all = $request->all();
    $updatedNotes = [];

    foreach ($all as $note) {
      if (isset($note["id"])) {
        $dbNoteItem = LocationNote::findOrFail($note["id"]);
        $dbNoteItem->posY = null;
        $dbNoteItem->save();
      } else {
        return $this->badRequest();
      }
    }

    foreach ($all as $note) {
      $dbNoteItem = LocationNote::findOrFail($note["id"]);
      $dbNoteItem->posY = $note["posY"];
      $dbNoteItem->pinned = $note["pinned"];
      $dbNoteItem->save();
      array_push($updatedNotes, $dbNoteItem);
    }

    return $this->singleJson($updatedNotes);
  }

  /**
   * adds a comment to a specific note
   *
   * @param $request the http request
   * @param $noteId the note id
   * 
   * @return JsonResponse
   */
  public function addComment(Request $request, $id)
  {
    $comment = new LocationNoteComment($request->all());
    $comment->locationNoteId = $id;
    $comment->userId = $this->getCurrentUserId();
    $comment->save();

    return $this->singleJson(DB::table('locations_notes_comments')
      ->where('locations_notes_comments.id', $comment->id)
      ->join('users', 'users.id', 'locations_notes_comments.userId')
      ->select([
        'locations_notes_comments.id',
        'locations_notes_comments.locationNoteId',
        'locations_notes_comments.content',
        'locations_notes_comments.created_at',
        'users.username'
      ])
      ->get()[0]);
  }

  /**
   * send email reply and add to location_mail
   *
   * @param $request the http request
   * @param $noteId the note id
   *
   * @return JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
   */
  public function sendMailReply(Request $request, $id)
  {
      $data = $request->all();
      $validator = Validator::make( $data, [
          'content' => 'required',
          'subject' => 'required',
      ]);

      if ($validator->fails()) {
          return $this->badRequestWithReason('Request parameters is missing');
      }

      $result = $this->locationNoteRepository->sendMailReply($id, $request);

      if ($result != null) {
        return $this->singleJson($result);
      } else {
        $this->badRequestWithReason('Request parameters is missing');
      }
  }

  /**
   * adds a note
   *
   * @param $request the http request
   * @return JsonResponse
   */
  public function addNote(Request $request)
  {
    $locationNote = new LocationNote($request->all());
    $locationNote->save();
    return $this->singleJson($this->locationNoteRepository->byId($locationNote->id));
  }

  /**
   * deletes a note
   *
   * @param $noteId the note id
   * 
   * @return JsonResponse
   */
  public function deleteNote($id)
  {
    $locationNoteToDelete = LocationNote::findOrFail($id);
    $locationNoteToDelete->delete();
    $locationNotesToUpdate = LocationNote::where('locationId', $locationNoteToDelete->locationId)->where('posY', '>', $locationNoteToDelete->posY)->orderBy('posY')->get();

    foreach ($locationNotesToUpdate as $locationNoteToUpdate) {
      $locationNoteToUpdate->posY = $locationNoteToUpdate->posY - 1;
      $locationNoteToUpdate->save();
    }
  }

  /**
   * deletes a comment of a specific note
   *
   * @param $id the comment id
   * 
   * @return JsonResponse
   */
  public function deleteComment($id)
  {
    LocationNoteComment::findOrFail($id)->delete();
  }
}
