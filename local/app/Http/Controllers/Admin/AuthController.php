<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class AuthController extends AdminController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (\Auth::guard('admin')->user() !== null) {
                return redirect()->route('admin.dashboard');
            }
            return $next($request);
        });
    }

    public function getChangeLocale(Request $request)
    {
        \Session::put('lang', $request->lang);
        return redirect()->back();
    }

    public function getLogin(Request $request)
    {
        return view('admin.auth.login');
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $remember = $request->remember == "on" ? true : false;
        $credentials = $request->only('email', 'password');

        if (!\Auth::guard('admin')->attempt($credentials, $remember)) {
            return errorMessage('username_password_not_exist');
        }

        $user = \Auth::guard('admin')->user();
        if ($user->status != 1) {
            \Auth::guard('admin')->logout();
            return errorMessage(($user->status == 0 ? 'account_inactive' : 'account_blocked'));
        }

        navigationMenuListing();

        return successMessage('logged_in_successfully');
    }

    public function getForgotPassword(Request $request)
    {
        return view('admin.auth.forgot_password');
    }

    public function postForgotPassword(Request $request)
    {
        $this->validate($request, [
            'email' => "required|email|exists:admins",
        ]);

        try {
            $dataArr = arrayFromPost(['email']);

            $admin = \App\Models\Admin::where('email', $dataArr->email)->first();
            if (blank($admin)) {
                return errorMessage('email_incorrect');
            } elseif ($admin->status != 1) {
                return errorMessage('account_inactive');
            }

            if (blank($admin->hash_token)) {
                $admin->hash_token = generateRandomString(50);
                $admin->save();
            }

            // Try to send email
            $configArr = [
                'name' => $admin->name,
                'reset_link' => route('admin.password.reset.request', ['token' => $admin->hash_token]),
            ];
            sendEmail('reset_password', 'Reset Password', $admin->email, $configArr);

            return successMessage('recovery_mail_send');
        } catch (\Throwable $th) {
            return exceptionErrorMessage($th);
        }
    }

    public function getResetPassword(Request $request)
    {
        $admin = \App\Models\Admin::where('hash_token', $request->token)->first();
        if (blank($admin)) {
            exit('This link has expired.');
        }
        return view('admin.auth.reset_password', compact('admin'));
    }

    public function postResetPassword(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric|exists:admins',
            'password' => 'required|confirmed|min:6',
        ]);

        try {
            $dataArr = arrayFromPost(['id', 'password']);

            $admin = \App\Models\Admin::find($dataArr->id);
            $admin->hash_token = null;
            $admin->password = bcrypt($dataArr->password);
            $admin->save();

            return successMessage('password_changed');
        } catch (\Throwable $th) {
            return exceptionErrorMessage($th);
        }
    }
}
