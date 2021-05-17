<?php
/**
 * by stephan scheide
 */

namespace App\Http\Controllers\Publics\Users;


use App\Entities\Core\InternUser;
use App\Http\Controllers\Publics\AbstractPublicsController;
use App\Repositories\Admin\InternUserRepository;
use Illuminate\Http\Request;

class PublicUserController extends AbstractPublicsController
{
    const SECRET_TOKEN = '848484-121212';

    private $internUserRepository;

    public function __construct(InternUserRepository $internUserRepository)
    {
        $this->internUserRepository = $internUserRepository;
    }

    /**
     * Erzeugt Benutzer von außen
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function createNarevUser(Request $request)
    {

        if (!$this->hasBearerInHeader($request, self::SECRET_TOKEN)) {
            return $this->ourResponse(null, 401);
        }

        $data = $request->json()->all();

        $nid = $data['narev-id'] * 1;

        $currentUser = $this->internUserRepository->byNarevId($nid);

        //Falls es den Benutzer (mittels NarevID) schon gibt, wird dieser aktualisiert
        if ($currentUser != null) {
            $currentUser->username = $data['username'];
            $currentUser->password = md5($data['password']);
            $currentUser->save();
            return $this->singleJson($currentUser);
        } //ansonsten wird dieser neu angelegt
        else {
            $user = new InternUser();
            $user->id = null;
            $user->username = $data['username'];
            $user->password = md5($data['password']);
            $user->narev_id = $data['narev-id'];
            $user->save();
            return $this->singleJson($user);
        }


    }

    public function setSingleTokenOfUser(Request $request, $id)
    {
        $all = $request->json()->all();
        /**
         * @var InternUser $user
         */
        $user = $this->internUserRepository->byNarevId($id);
        if ($user == null) {
            return $this->notFound();
        }
        $user->narev_token = $all['token'];
        $user->save();

        return $this->noContent();
    }
}
