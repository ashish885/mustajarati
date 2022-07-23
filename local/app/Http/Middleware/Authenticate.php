<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('admin.login');

            /* $pathInfo = explode('/', ltrim($request->getPathInfo(), '/'))[0];
            if ($pathInfo == 'admin') {
                return route('admin.login');
            } else {
                // Do something here
                // return route('web.home.index');
            } */
        }
    }
}
