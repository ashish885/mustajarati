<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            $this->locale = getSessionLang();
            $ql = $this->locale == 'ar' ? '' : "{$this->locale}_";

            $switcherLocale = $this->locale == 'ar' ? 'en' : 'ar';

            $dialCodes = \App\Models\Country::select(\DB::raw("{$ql}name AS name, dial_code"))
                ->where('status', 1)
                ->groupBy('dial_code')
                ->get();

            \View::share('locale', $this->locale);
            \View::share('ql', $ql);
            \View::share('switcherLocale', $switcherLocale);
            \View::share('dialCodes', $dialCodes);

            \View::share('showContentOnly', false);

            \View::share('play_store_url', 'https://play.google.com/store/apps/details?id=com.htf.mustajarati');
            \View::share('apple_store_url', 'https://apps.apple.com/us/app/mustajarati-%D9%85%D8%B3%D8%AA%D8%A3%D8%AC%D8%B1%D8%A7%D8%AA%D9%8A/id1586628159');

            return $next($request);
        });
    }
}
