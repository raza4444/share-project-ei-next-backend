<?php
namespace App\Entities\Mail;

use App\Entities\Core\AbstractModel;

/**
 * Class MailPlaceholders
 * @package App\Entities\Mail
 *
 * @property string content
 *
 */
class MailPlaceholders extends AbstractModel
{
    protected $table = 'mail_placeholders';
    protected $fillable = ['name'];
}