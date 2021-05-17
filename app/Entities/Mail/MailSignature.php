<?php

namespace App\Entities\Mail;


use App\Entities\Core\AbstractModel;

/**
 * Class MailSignature
 * @package App\Entities\Mail
 *
 * @property string content
 *
 */
class MailSignature extends AbstractModel
{
    protected $table = 'mail_signatures';

    protected $fillable = ['name','content'];

    public function templates()
    {
        return $this->hasMany(MailTemplate::class, 'signature_id', 'id');
    }
}