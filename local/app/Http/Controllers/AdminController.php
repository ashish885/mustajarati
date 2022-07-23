<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {

            $this->locale = getSessionLang();
            $this->ql = getCustomSessionLang();

            if (blank(\Session::get('navigation_admin'))) {
                navigationMenuListing();
            }

            \View::share('locale', $this->locale);
            \View::share('ql', $this->ql);
            \View::share('admin', auth()->user());
            \View::share('navMenu', \Session::get('navigation_admin'));

            return $next($request);
        });
    }
}
