<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use App\Models\Villager;
use Closure;

class VillagerAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headers = $request->headers->all();
        if (!isset($headers['authorization']) || $headers['authorization'] == '') {
            return ResponseHelper::makeError('Unauthorized', ['message' => 'Authorization required'], 401);
        }
        $authorization = $headers['authorization'][0];
        if (strpos($authorization, 'Bearer') !== 0) {
            return ResponseHelper::makeError('Unauthorized', ['message' => 'Authorization required'], 401);
        }
        $cleanAuthorization = str_replace('Bearer ', '', $authorization);
        $villager = Villager::whereAccessToken($cleanAuthorization)->first();
        if ($villager == null) {
            return ResponseHelper::makeError('Unauthorized', ['message' => 'Authorization required'], 401);
        }
        /**Todo: validate for expiration
         **/
        return $next($request);
    }
}
