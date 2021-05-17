<?php

namespace App\Entities\Branches;

use App\Entities\Branches\Location;
use App\Entities\Core\AbstractModel;
use App\User;

/**
 * Class MailSignature
 * @package App\Entities\Mail
 *
 * @property string content
 *
 */
class LocationMail extends AbstractModel
{
    protected $table = 'location_mails';

    protected $fillable = ['to', 'from', 'user_id', 'subject', 'content', 'location_id', 'message_id'];


    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function note()
    {
        return $this->hasOne(LocationNote::class, 'location_mail_id', 'id');
    }
}