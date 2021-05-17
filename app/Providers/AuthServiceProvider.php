<?php

namespace App\Providers;

use App\Entities\Core\InternUser;
use App\Logging\QuickLog;
use App\Services\Core\ApiTokenService;
use App\Services\Core\CurrentUserService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {

        /**
         * @var $service ApiTokenService
         */
        $service = $this->app->make(ApiTokenService::class);

        /**
         * @var $currentUserService CurrentUserService
         */
        $currentUserService = $this->app->make(CurrentUserService::class);

        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a InternUser instance or null. You're free to obtain
        // the InternUser instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) use ($service, $currentUserService) {

            /**
             * @var $request Request
             */

            $token = $request->header('apitoken');

            if (!empty($token)) {
                /**
                 * @var InternUser $user
                 */
                $user = $service->findUserByToken($token);
                if ($user != null) {
                    $line = 'v1 ' . $user->id . ' ' . $user->username . ' ' . $request->getMethod() . ' ' . $request->getPathInfo();
                    QuickLog::quickWithName('intern-user-action', $line);
                    $user->updateLastActionToNow();
                    $currentUserService->assignCurrentUser($user, $token);
                }
                return $user;
            }
        });
    }
}
