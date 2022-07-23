<?php

namespace App\Http\Controllers\API\User\V1;

use App\Http\Controllers\API\User\UserController;
use Illuminate\Http\Request;

class ProfileController extends UserController
{
    public function getDetails(Request $request)
    {
        $user = getTokenUser();

        try {
            return apiResponse('success', processUserResponseData($user));
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }

    public function postUpdateProfile(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'name' => 'required|max:250',
            'email' => "required|email",
            'dial_code' => 'required|numeric|exists:countries,dial_code',
            'mobile' => 'required|numeric|digits_between:6,20',
            // 'national_id' => 'required',
            // 'national_id_front_image' => 'nullable|' . config('cms.allowed_image_mimes'),
        ]);
        $dataArr = arrayFromPost(['name', 'email', 'dial_code', 'mobile', 'national_id']);

        try {
            // Check Mobile No Duplicate
            $dataArr->mobile = ltrim($dataArr->mobile, '0');
            if (\App\Models\User::where('dial_code', $dataArr->dial_code)->where('mobile', $dataArr->mobile)->where('id', '<>', $user->id)->exists()) {
                return errorMessage('mobile_already_taken');
            }

            $user->name = $dataArr->name;
            $user->email = strtolower($dataArr->email);
            $user->dial_code = $dataArr->dial_code;
            $user->mobile = $dataArr->mobile;
            // $user->national_id = $dataArr->national_id;
            // if ($request->national_id_front_image) {
            //     $user->national_id_front_image = uploadFile('national_id_front_image');
            // }
            $user->save();

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }

    public function postUpdateImage(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'image' => config('cms.allowed_image_mimes'),
        ]);

        try {
            $user->profile_image = 'default-user.png';
            if ($request->image) {
                $user->profile_image = uploadFile('image');
                if (blank($user->profile_image)) {
                    return errorMessage('file_uploading_failed');
                }
            }
            $user->save();

            return apiResponse('success', ['image' => $user->profile_image]);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }

    public function postUpdatePassword(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'old_password' => 'required|min:6',
            'password' => 'required|min:6',
        ]);
        $dataArr = arrayFromPost(['old_password', 'password']);

        try {
            if (!\Hash::check($dataArr->old_password, $user->password)) {
                return errorMessage('invalid_old_password');
            }

            $user->password = bcrypt($dataArr->password);
            $user->save();

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }

    public function postUpdateFcmToken(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'fcm_id' => 'required',
            'device_id' => 'required',
            'device_type' => 'required|in:android,ios',
        ]);
        $dataArr = arrayFromPost(['fcm_id', 'device_id', 'device_type']);

        try {
            $fcmToken = new \stdClass;
            $fcmToken->user_id = $user->id;
            $fcmToken->fcm_id = $dataArr->fcm_id;
            $fcmToken->device_id = $dataArr->device_id;
            $fcmToken->device_type = $dataArr->device_type;
            updateFCMToken($fcmToken);

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }
}
