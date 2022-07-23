<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class AutoAddressController extends AdminController
{
    public function googleAutoAddress()
    {
        return view('admin.location.index');
    }
}
