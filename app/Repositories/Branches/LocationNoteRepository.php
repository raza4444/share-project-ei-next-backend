<?php

namespace App\Repositories\Branches;

use App\Entities\Branches\LocationNote;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Facades\DB;
use App\Entities\Core\PermissionType;
use App\Entities\Branches\LocationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Mail\MailService;


class LocationNoteRepository extends AbstractRepository
{
    private $mailService;

  /**
   * LocationNoteRepository constructor.
   * @param MailService $mailService
   */
  public function __construct(MailService $mailService)
  {
    $this->mailService = $mailService;
    parent::__construct(LocationNote::class);
  }

  public function byId($id)
  {
    $locationNote = DB::table('locations_notes')
      ->where('locations_notes.id', $id)
      ->join('users', 'users.id', 'locations_notes.userId')
      ->select([
        'locations_notes.id',
        'locations_notes.title',
        'locations_notes.content',
        'locations_notes.posX',
        'locations_notes.posY',
        'locations_notes.pinned',
        'locations_notes.updated_at',
        'users.username',
        'locations_notes.location_mail_id'
      ])
      ->get()[0];

      $locationNote = $this->loadMailData($locationNote);

      $notesComments = DB::table('locations_notes_comments')
        ->where('locationNoteId', $locationNote->id)
        ->join('users', 'users.id', 'locations_notes_comments.userId')
        ->orderBy('created_at', 'desc')
        ->select([
          'locations_notes_comments.id',
          'locations_notes_comments.content',
          'locations_notes_comments.created_at',
          'users.username'
        ])
        ->get();

    $locationNote->comments = $notesComments;

    return $locationNote;
  }

  public function byLocationId($locationId)
  {
    $locationNotes = DB::table('locations_notes')
      ->where('locationId', $locationId)
      ->leftJoin('users', 'users.id', 'locations_notes.userId')
      ->orderBy('locations_notes.posY', 'asc')
      ->select([
        'locations_notes.id',
        'locations_notes.title',
        'locations_notes.content',
        'locations_notes.posX',
        'locations_notes.posY',
        'locations_notes.pinned',
        'locations_notes.updated_at',
        'users.username',
        'locations_notes.location_mail_id'
      ])->get();

    foreach ($locationNotes as $locationNote) {

      $locationNote = $this->loadMailData($locationNote);

      $notesComments = DB::table('locations_notes_comments')
        ->where('locationNoteId', $locationNote->id)
        ->join('users', 'users.id', 'locations_notes_comments.userId')
        ->orderBy('created_at', 'asc')
        ->select([
          'locations_notes_comments.id',
          'locations_notes_comments.content',
          'locations_notes_comments.created_at',
          'users.username'
        ])
        ->get();

      $locationNote->comments = $notesComments;

    }

    return $locationNotes;
  }

  public function update($locationNoteId, $values)
  {
    $locationNote = LocationNote::findOrFail($locationNoteId);
    $locationNote->update($values);
    $locationNote = $this->loadMailData($locationNote);
    return $locationNote;
  }

  public function countAll($locationId)
  {
    return LocationNote::where('locationId', $locationId)->count();
  }

  public function getPermissionOfNotesOfCompanyBlock()
  {
    return [
      PermissionType::COMPANY_DETAILS_NOTES_FOR_COMPANY_BLOCK_SHOW,
      PermissionType::COMPANY_DETAILS_NOTES_FOR_COMPANY_BLOCK_ADD,
      PermissionType::COMPANY_DETAILS_NOTES_FOR_COMPANY_BLOCK_EDIT,
    ];
  }

  /**
   * send email reply and add to location_mail
   *
   * @param $request the http request
   * @param $noteId the note id
   *
   * @return JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
   */
  public function sendMailReply($noteId, Request $request)
  {
    $locationNote = LocationNote::findOrFail($noteId);
      if ($locationNote->location_mail_id && $locationNote->mail->message_id) {
          $mail = $locationNote->mail;
          $toEmail = $mail->from === env('MAIL_USERNAME')? $mail->to: $mail->from;
          $subject = str_contains($mail->subject,'Re: ')? $mail->subject: 'Re: '.$mail->subject;
          $data = [
              'to' => $toEmail,
              'subject' => $request->get('subject', $subject),
              'content' => $request->get('content', 'Empty Content'),
          ];
          $this->mailService->sendReplyEmail($data);

          $locationMail = new LocationMail();
          $locationMail->to = $toEmail;
          $locationMail->from = env('MAIL_USERNAME');
          $locationMail->subject = $data['subject'];
          $locationMail->content = $data['content'];
          $locationMail->location_id = $locationNote['locationId'];
          $locationMail->location_mail_id = $mail->id;
          $locationMail->user_id = Auth::user()->id;
          $locationMail->save();

          return $locationMail;
      } else {
          return null;
      }
  }

  private function loadMailData($locationNote)
  {
    if ($noteEmailId = $locationNote->location_mail_id) {
      $locationMail = DB::table('location_mails')->where('id', $noteEmailId)->get()[0];
      $locationNote->content = $locationMail->content;
      $locationNote->title = $locationMail->subject;
      $locationNote->email = $locationMail->message_id != null ? $locationMail->from : null;
    }

    return $locationNote;
  }
}
