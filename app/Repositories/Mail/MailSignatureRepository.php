<?php
namespace App\Repositories\Mail;

use App\Entities\Mail\MailSignature;
use App\Repositories\AbstractRepository;

/**
 * Class MailSignatureRepository
 * @package  App\Repositories\Mail
 *
 * @property string content
 *
 */
class MailSignatureRepository extends AbstractRepository
{
    public function __construct() {}

    public function create($data) {

        $mailSignature = new MailSignature([
            'name' => $data['name'],
            'content' => $data['content']
        ]);
        $mailSignature->save();

        return $mailSignature;
    }

    public function getList()
    {
        return MailSignature::all();
    }

    public function byId($id)
    {
        
        $mailSignature = MailSignature::where('id', '=', $id)->first();
        if ($mailSignature == null) {
            return null;
        }
        return $mailSignature;
    }

    public function update(MailSignature $mailSignature, $data)
    {
        $mailSignature->name    = $data['name'];
        $mailSignature->content = $data['content'];

        $mailSignature->save();
        return $mailSignature->get();
    }

    public function delete(MailSignature $mailSignature)
    {
        $mailSignature->delete();
        return $mailSignature;
    }

    public function isNameExist($name) {
     $existingName =  MailSignature::where('name', $name)->count();
     if($existingName === 0) {
         return false;
     } else {
         return true;
     }
    }
}