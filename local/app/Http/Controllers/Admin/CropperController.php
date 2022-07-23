<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class CropperController extends AdminController
{
    public function getIndex(Request $request)
    {
        return view('admin.cropper.index', [
            'width' => $request->width,
            'height' => $request->height,
            'name' => $request->name,
            'enable_ratio' => $request->enable_ratio,
        ]);
    }
}
