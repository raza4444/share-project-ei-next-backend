<?php

namespace App\Entities\Mail;


use App\Entities\Core\AbstractModel;

/**
 * Class MailTemplate
 * @package App\Entities\Mail
 *
 * @property string content
 *
 */
class MailTemplate extends AbstractModel
{
    protected $table = 'mail_templates';

    protected $fillable = ['subject', 'content', 'signature_id'];

    public function signature()
    {
        return $this->belongsTo(MailSignature::class, 'signature_id', 'id');
    }
}