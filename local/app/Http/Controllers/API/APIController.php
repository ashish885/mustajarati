<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class APIController extends Controller
{
    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {
            $this->locale = $request->locale;
            $this->ql = $request->locale == 'ar' ? '' : "{$request->locale}_";
            return $next($request);
        });
    }
}
