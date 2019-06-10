<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use App;
use Config;

class SetLocale
{
    /**
     *
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = Lang::getLocale();
        if (Auth::check()) {
            $currentLocale = Auth::user()->language_key;
            if ($currentLocale !== '' || $currentLocale !== null) {
                $locale = $currentLocale;
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}
