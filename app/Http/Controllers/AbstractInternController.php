<?php
/**
 * Created by PhpStorm.
 * User: kingster
 * Date: 16.12.2018
 * Time: 15:47
 */

namespace App\Http\Controllers;


use App\Logging\QuickLog;
use App\Services\Core\CurrentUserService;
use Illuminate\Support\Facades\App;
use Laravel\Lumen\Routing\Controller;

class AbstractInternController extends Controller
{
    use RestResultTrait;

    protected function getCurrentUserId()
    {
        /**
         * @var $service CurrentUserService
         */
        $service = App::make(CurrentUserService::class);
        return $service->getCurrentUser()->id;
    }

    /**
     * returns current user
     * @return \App\Entities\Core\InternUser
     */
    protected function getCurrentUser()
    {
        /**
         * @var $service CurrentUserService
         */
        $service = App::make(CurrentUserService::class);
        return $service->getCurrentUser();
    }

    protected function debug($line)
    {
        $c = new \ReflectionClass($this);
        $name = $c->getShortName();
        QuickLog::appendToSingleLog($name, $line);
    }


}
