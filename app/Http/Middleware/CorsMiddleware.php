<?php
/**
 * by stephan scheide
 */

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{

    public function handle($request, Closure $next)
    {

        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE, PATCH',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '86400',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, apitoken',
            'Expires' => 'Tue, 03 Jul 2001 06:00:00 GMT',
            'Last-Modified' => '{now} GMT',
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate'
        ];

        if ($request->isMethod('OPTIONS')) {
            return response()->json('{"method":"OPTIONS"}', 200, $headers);
        }

        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }


}