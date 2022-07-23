<?php

namespace App\Http\Middleware;

use Closure;

class CheckApiLocale
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
        if (!in_array($request->header('locale'), getLocales())) {
            return errorMessage('invalid_locale');
        }

        \App::setLocale($request->header('locale'));
        $request->request->add(['locale' => $request->header('locale')]);

        return $next($request);
    }
}
