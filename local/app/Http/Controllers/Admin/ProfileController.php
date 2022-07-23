<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class ProfileController extends AdminController
{
    public function getDetails(Request $request)
    {
        $admin = auth()->user();
        return view('admin.profile', compact('admin'));
    }

    public function postUpdate(Request $request)
    {
        $admin = auth()->user();
        $this->validate($request, [
            'name' => 'required',
            'email' => "required|email|unique:admins,id,{$admin->id}",
            'mobile' => 'required|numeric|digits_between:9,20',
            'profile_image' => config('cms.allowed_image_mimes'),
        ]);
        $dataArr = arrayFromPost(['name', 'email', 'mobile']);

        try {
            $admin->name = $dataArr->name;
            $admin->email = strtolower($dataArr->email);
            $admin->mobile = $dataArr->mobile;
            if ($request->profile_image) {
                $admin->profile_image = uploadFile('profile_image');
            }
            $admin->save();
            return successMessage();
        } catch (\Throwable $th) {
            return exceptionErrorMessage($th);
        }
    }

    public function postChangePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required|min:6',
            'password' => 'required|confirmed|min:6',
        ]);
        $dataArr = arrayFromPost(['old_password', 'password']);

        try {
            $admin = auth()->user();

            if (!\Hash::check($dataArr->old_password, $admin->password)) {
                return errorMessage('invalid_old_password');
            }

            $admin->password = bcrypt($dataArr->password);
            $admin->save();

            return successMessage('password_changed');
        } catch (\Throwable $th) {
            return exceptionErrorMessage($th);
        }
    }

    public function getLogout(Request $request)
    {
        auth()->logout();
        return redirect()->route('admin.login');
    }
}
