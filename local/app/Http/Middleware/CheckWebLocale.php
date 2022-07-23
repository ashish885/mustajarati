<?php

namespace App\Http\Middleware;

use Closure;

class CheckWebLocale
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
        if (!(\Session::has('lang') && in_array(\Session::get('lang'), getLocales()))) {
            $locale = env('APP_LOCALE');
            \Session::put('lang', $locale);
        } else {
            $locale = \Session::get('lang');
        }

        \App::setLocale($locale);
        $request->request->add(['locale' => $locale]);
        return $next($request);
    }
}
