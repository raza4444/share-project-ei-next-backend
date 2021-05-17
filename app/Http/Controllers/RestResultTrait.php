<?php
/**
 * Created by PhpStorm.
 * User: kingster
 * Date: 16.12.2018
 * Time: 15:42
 */

namespace App\Http\Controllers;


use App\Entities\Core\AbstractModel;
use Illuminate\Http\Request;

trait RestResultTrait
{

    public function ourResponse($content, $code)
    {
        return response($content, $code);
    }

    public function accessDenied()
    {
        return $this->ourResponse(null, 403);
    }

    public function accessDeniedWithReason($reason)
    {
        return $this->ourResponse($this->_reason($reason), 403);
    }

    public function badRequest()
    {
        return $this->ourResponse(null, 400);
    }

    public function notFound()
    {
        return $this->ourResponse(null, 404);
    }

    public function notFoundWithReason($reason)
    {
        return $this->ourResponse($this->_reason($reason), 404);
    }

    public function badRequestWithReason($reason)
    {
        return $this->ourResponse($this->_reason($reason), 400);
    }

    public function serverError($reason)
    {
        return $this->ourResponse($this->_reason($reason), 500);
    }

    public function noContent()
    {
        return $this->ourResponse(null, 204);
    }

    public function created()
    {
        return $this->ourResponse(null, 201);
    }

    public function conflict()
    {
        return $this->ourResponse(null, 409);
    }

    public function conflictWithReason($reason)
    {
        return $this->ourResponse($reason, 409);
    }

    /**
     * returns single entity as json
     * @param $something
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function singleJson($something, $code = 200)
    {
        return response()->json($something)->setStatusCode($code);
    }

    /**
     * transform content to json and returns it
     *
     * @param $code
     * @param $content
     * @return \Illuminate\Http\JsonResponse
     */
    public function json($code, $content)
    {
        return $this->singleJson($content, $code);
    }

    public function serverErrorQuick($message)
    {
        return $this->json(500, ['message' => $message]);
    }

    public function allInput(Request $request)
    {
        if ($request->isJson()) {
            return $request->json()->all();
        } else {
            return $request->all();
        }
    }

    /**
     * @param Request $request
     * @param $clazz
     * @return AbstractModel
     */
    public function jsonAsEntity(Request $request, $clazz)
    {
        $data = $this->allInput($request);
        $obj = new $clazz();
        $obj->fill($data);
        return $obj;
    }

    public function entityUpdate(Request $request, $model)
    {
        if ($model == null) {
            return $this->notFound();
        }
        $data = $this->allInput($request);
        /**
         * @var AbstractModel $model
         */
        $model->fill($data);
        $model->save();
        return $this->singleJson($model);
    }

    public function entityCreated(AbstractModel $entity)
    {
        return $this->singleJson($entity->getAttributes(), 201);
    }

    public function entityDelete($entity)
    {
        if ($entity != null) {
            $entity->delete();
        }
        return $this->noContent();
    }

    public function hasBearerInHeader(Request $request, $secretToken)
    {
        $auth = $request->header('Authorization');
        if (strpos($auth, 'Bearer ') === 0) {
            $token = substr($auth, 7);
            if ($token == $secretToken) {
                return true;
            }
        }

        return false;

    }

    private function _reason($r)
    {
        return ['reason' => $r];
    }

}
