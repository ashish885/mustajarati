<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class UserController extends AdminController
{
    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.users.index'), 401);

        return view('admin.users.index');
    }

    public function getList(Request $request)
    {
        $users = \App\Models\User::select('*')
            ->when(!blank($request->from_date) && !blank($request->to_date), function ($query) use ($request) {
                $query->whereBetween('users.created_at', [$request->from_date, $request->to_date]);
            });

        return \DataTables::of($users)
            ->addColumn('status_text', function ($query) {
                return transLang('action_status')[$query->status];
            })
            ->make();
    }

    public function getCreate(Request $request)
    {
        abort_unless(hasPermission('admin.users.create'), 401);

        $dial_codes = \App\Models\Country::select(\DB::raw("dial_code, CONCAT(dial_code, ' (', {$this->ql}name, ')') AS text"))
            ->where('status', 1)
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.users.create', compact('dial_codes'));
    }

    public function postCreate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'dial_code' => 'required|numeric|digits_between:1,5',
            'mobile' => 'required|numeric|digits_between:9,20',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'profile_image' => config('cms.allowed_image_mimes'),
        ]);
        $dataArr = arrayFromPost(['name', 'email', 'dial_code', 'mobile', 'password', 'status']);

        // Check Mobile No Duplicate
        if (\App\Models\User::where('dial_code', $dataArr->dial_code)->where('mobile', ltrim($dataArr->mobile, '0'))->count()) {
            return errorMessage('mobile_already_taken');
        }

        try {
            // Start Transaction
            \DB::beginTransaction();

            $user = new \App\Models\User();
            $user->name = $dataArr->name;
            $user->email = strtolower($dataArr->email);
            $user->password = bcrypt($request->password);
            $user->dial_code = $dataArr->dial_code;
            $user->mobile = ltrim($dataArr->mobile, '0');
            $user->status = $dataArr->status;
            $user->profile_image = uploadFile('profile_image');
            $user->hash_token = generateRandomString(25);
            $user->save();

            // Commit Transaction
            \DB::commit();
            return successMessage();
        } catch (\Throwable $th) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($th);
        }
    }

    public function getUpdate(Request $request)
    {
        abort_unless(hasPermission('admin.users.update'), 401);

        $user = \App\Models\User::findOrFail($request->id);
        $dial_codes = \App\Models\Country::select(\DB::raw("dial_code, CONCAT(dial_code, ' (', {$this->ql}name, ')') AS text"))
            ->where('status', 1)
            ->orWhere('dial_code', $user->dial_code)
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.users.update', compact('user', 'dial_codes'));
    }

    public function postUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'dial_code' => 'required|numeric|digits_between:1,5',
            'mobile' => 'required|numeric|digits_between:9,20',
            'email' => 'required|email',
            'profile_image' => config('cms.allowed_image_mimes'),
        ]);
        $dataArr = arrayFromPost(['name', 'email', 'dial_code', 'mobile', 'status']);

        // Check Mobile No Duplicate
        if (\App\Models\User::where('dial_code', $dataArr->dial_code)->where('mobile', ltrim($dataArr->mobile, '0'))->where('id', '<>', $request->id)->exists()) {
            return errorMessage('mobile_already_taken');
        }

        try {
            // Start Transaction
            \DB::beginTransaction();

            $user = \App\Models\User::find($request->id);
            $user->name = $dataArr->name;
            $user->email = strtolower($dataArr->email);
            $user->dial_code = $dataArr->dial_code;
            $user->mobile = ltrim($dataArr->mobile, '0');
            $user->status = $dataArr->status;
            if ($request->profile_image) {
                $user->profile_image = uploadFile('profile_image');
            }
            $user->save();

            // Commit Transaction
            \DB::commit();
            return successMessage();
        } catch (\Throwable $th) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($th);
        }
    }

    public function getDelete(Request $request)
    {
        abort_unless(hasPermission('admin.users.delete'), 401);

        $user = \App\Models\User::find($request->id)->delete();
        return successMessage();
    }
    public function productReviewsDelete(Request $request)
    {
        abort_unless(hasPermission('admin.users.products_reviews.delete'), 401);

        $user = \App\Models\ProductReview::find($request->id)->delete();
        return successMessage();
    }
    public function serviceReviewsDelete(Request $request)
    {
        abort_unless(hasPermission('admin.users.service_reviews.delete'), 401);

        $user = \App\Models\ServiceReview::find($request->id)->delete();
        return successMessage();
    }

    public function getView(Request $request)
    {
        $user = \App\Models\User::findOrFail($request->id);
        return view('admin.users.view', compact('user'));
    }

    public function getPasswordReset(Request $request)
    {
        abort_unless(hasPermission('admin.users.password_reset'), 401);

        $user = \App\Models\User::findOrFail($request->id);
        return view('admin.users.password-reset', compact('user'));
    }

    public function postPasswordReset(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            $user = \App\Models\User::find($request->id);
            $user->password = bcrypt($request->password);
            $user->save();

            return successMessage('password_changed');
        } catch (\Throwable $th) {
            return exceptionErrorMessage($th);
        }
    }
}
