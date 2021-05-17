<?php
namespace App\Repositories\Mail;

use App\Entities\Mail\MailTemplate;
use App\Entities\Mail\MailPlaceholders;
use App\Entities\Core\MailPlaceholdersConstants;

use App\Repositories\AbstractRepository;

/**
 * Class MailTemplateRepository
 * @package  App\Repositories\Mail
 *
 * @property string content
 *
 */
class MailTemplateRepository extends AbstractRepository
{
    public function __construct() {}

    public function create($data) {

        $mailTemplate = new MailTemplate([
            'subject' => $data['subject'],
            'content' => $data['content'],
            'signature_id' => $data['signature_id']
        ]);
        $mailTemplate->save();

        return $mailTemplate;
    }

    public function getList()
    {
        return MailTemplate::with('signature')->get();
    }

    public function byId($id)
    {
        
        $mailTemplate = MailTemplate::where('id', '=', $id)->with('signature')->first();
        if ($mailTemplate == null) {
            return null;
        }
        return $mailTemplate;
    }

    public function update(MailTemplate $mailTemplate, $data)
    {
        $mailTemplate->subject = $data['subject'];
        $mailTemplate->content = $data['content'];
        $mailTemplate->signature_id = $data['signature_id'];

        $mailTemplate->save();
        return $mailTemplate->with('signature')->get();
    }

    public function delete(MailTemplate $mailTemplate)
    {
        $mailTemplate->delete();
        return $mailTemplate;
    }

    public function mailPlaceholders() {
        return [
            MailPlaceholdersConstants::CUSTOMER_NUMBER,
            MailPlaceholdersConstants::CUSTOMER_GREEDING,
            MailPlaceholdersConstants::CUSTOMER_FIRST_NAME,
            MailPlaceholdersConstants::CUSTOMER_LAST_NAME,
            MailPlaceholdersConstants::COMPANY_NAME,
            MailPlaceholdersConstants::REGISTRATION_USERNAME,
            MailPlaceholdersConstants::REGISTRATION_PASSWORD,
            MailPlaceholdersConstants::REGISTRATION_DOMAIN,
            MailPlaceholdersConstants::FTP_USERNAME,
            MailPlaceholdersConstants::FTP_PASSWORD,
            MailPlaceholdersConstants::FTP_DIRECTORY_HTML
        ];
    }
    
    public function getMailPlaceholders() {
       return MailPlaceholders::all();
    }
}