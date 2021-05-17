<?php
namespace App\Repositories\Branches;

use App\Entities\Branches\LocationMail;
use App\Repositories\AbstractRepository;

/**
 * Class MailSignatureRepository
 * @package  App\Repositories\Mail
 *
 * @property string content
 *
 */
class LocationMailRepository extends AbstractRepository
{
    public function __construct() {}

    public function create($data) {
        $mail = [
            'subject' => $data['subject'],
            'content' => $data['content'],
            'location_id' => $data['location_id']
        ];

        if (array_key_exists('from', $data)) {
            $mail['from'] = $data['from'];
            $mail['message_id'] = $data['message_id'];
        } else {
            $mail['to'] = $data['to'];
            $mail['user_id'] = $data['user_id'];
        }

        $locationMail = LocationMail::create($mail);

        return $locationMail;
    }

    public function getListByLocationId($locationId)
    {
        return LocationMail::with('user')->where('location_id', '=', $locationId)->get();
    }

    public function findByToEmail($email)
    {
        $locationMail = LocationMail::where('to', '=', $email)->first();
        if ($locationMail == null) {
            return null;
        }
        return $locationMail;
    }

    public function byId($id)
    {
        
        $locationMail = LocationMail::where('id', '=', $id)->first();
        if ($locationMail == null) {
            return null;
        }
        return $locationMail;
    }

    public function byMessageId($messageId)
    {
        
        $locationMail = LocationMail::where('message_id', '=', $messageId)->first();
        return $locationMail != null;
    }

    /**
     * @return LocationMail
     */
    public function delete(LocationMail $locationMail)
    {
        $locationMail->delete();
        return $locationMail;
    }

    public function getLastIncomingEmailDate() {
        $date = LocationMail::where('message_id', '<>', null)->latest()->first();
        return isset($date) ? $date['created_at'] : null;
    }
}