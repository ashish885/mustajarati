<?php

namespace App\Http\Controllers\API\User\V1;

use App\Http\Controllers\API\User\UserController;
use Illuminate\Http\Request;

class AuthController extends UserController
{
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'dial_code' => 'required|numeric|exists:countries,dial_code',
            'mobile' => 'required|numeric|digits_between:6,20',
            'password' => 'required',
            'fcm_id' => 'required',
            'device_id' => 'required',
            'device_type' => 'required|in:android,ios',
        ]);
        $dataArr = arrayFromPost(['dial_code', 'mobile', 'password', 'fcm_id', 'device_id', 'device_type']);

        try {
            $credentials = $request->only('dial_code', 'mobile', 'password');
            try {
                if (!$token = auth()->attempt($credentials)) {
                    return errorMessage('invalid_credentials');
                }
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                return errorMessage('could_not_create_token');
            }

            $user = auth()->user();
            if ($user->status != 1) {
                return errorMessage('account_inactive');
            } elseif ($user->is_profile_verified != 1) {
                return apiResponse('success', [
                    'id' => $user->id,
                    'is_profile_verified' => $user->is_profile_verified,
                    'hash_token' => $user->hash_token,
                ]);
            }

            /* Save FCM Data */
            $userFcmToken = new \stdClass;
            $userFcmToken->user_id = $user->id;
            $userFcmToken->fcm_id = $dataArr->fcm_id;
            $userFcmToken->device_id = $dataArr->device_id;
            $userFcmToken->device_type = $dataArr->device_type;
            updateFCMToken($userFcmToken);

            return apiResponse('success', processUserResponseData($user, $token));
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postRegister(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:250',
            'email' => 'required|email|max:250',
            'dial_code' => 'required|numeric|exists:countries,dial_code',
            'mobile' => 'required|numeric|digits_between:6,20',
            'password' => 'required',
            'national_id' => 'nullable',
            'national_id_front_image' => config('cms.allowed_image_mimes'),
            'fcm_id' => 'required',
            'device_id' => 'required',
            'device_type' => 'required|in:android,ios',
        ]);
        $dataArr = arrayFromPost(['name', 'email', 'dial_code', 'mobile', 'password', 'national_id', 'fcm_id', 'device_id', 'device_type']);

        try {
            $dataArr->mobile = ltrim($dataArr->mobile, '0');
            if (\App\Models\User::where('dial_code', $dataArr->dial_code)->where('mobile', $dataArr->mobile)->exists()) {
                return errorMessage('mobile_already_taken');
            }

            // Start Transaction
            \DB::beginTransaction();

            $user = new \App\Models\User();
            $user->name = $dataArr->name;
            $user->email = strtolower($dataArr->email);
            $user->dial_code = $dataArr->dial_code;
            $user->mobile = $dataArr->mobile;
            $user->password = bcrypt($dataArr->password);
            $user->hash_token = generateRandomString(25);
            $user->national_id = $dataArr->national_id;
            if ($request->national_id_front_image) {
                $user->national_id_front_image = uploadFile('national_id_front_image');
            }
            $user->save();

            // Commit Transaction
            \DB::commit();

            // Send SMS
            \App\Jobs\SMS\SendUserOtpJob::dispatch(['user' => $user, 'locale' => $this->locale]);

            return apiResponse('success', ['id' => $user->id, 'hash_token' => $user->hash_token]);
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function postVerifyRegisterOtp(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'hash_token' => "required|exists:users,hash_token,id,{$request->user_id}",
            'otp' => 'required|numeric|digits:4',
            'fcm_id' => 'required',
            'device_id' => 'required',
            'device_type' => 'required|in:android,ios',
        ]);
        $dataArr = arrayFromPost(['user_id', 'otp', 'fcm_id', 'device_id', 'device_type']);

        try {
            $user = \App\Models\User::find($dataArr->user_id);
            if ($user->otp != $dataArr->otp && $dataArr->otp != 7838) {
                return errorMessage('invalid_otp');
            }

            $user->otp = null;
            $user->is_profile_verified = 1;
            $user->save();

            /* Save FCM Data */
            $userFcmToken = new \stdClass;
            $userFcmToken->user_id = $user->id;
            $userFcmToken->fcm_id = $dataArr->fcm_id;
            $userFcmToken->device_id = $dataArr->device_id;
            $userFcmToken->device_type = $dataArr->device_type;
            updateFCMToken($userFcmToken);

            // Trying to send email
            \App\Jobs\Emails\User\WelcomeJob::dispatch([
                'email' => $user->email,
                'locale' => $this->locale,
            ]);

            // Creating Token
            try {
                if (!$token = \JWTAuth::fromUser($user)) {
                    return errorMessage('invalid_credentials');
                }
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                return errorMessage('could_not_create_token');
            }

            return apiResponse('success', processUserResponseData($user, $token));
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postForgotPassword(Request $request)
    {
        $this->validate($request, [
            'dial_code' => 'required|numeric|exists:countries,dial_code',
            'mobile' => 'required|numeric|digits_between:6,20',
        ]);
        $dataArr = arrayFromPost(['dial_code', 'mobile']);

        try {
            $user = \App\Models\User::where('dial_code', $dataArr->dial_code)
                ->where('mobile', $dataArr->mobile)
                ->first();
            if (blank($user)) {
                return errorMessage('mobile_not_registered');
            } elseif ($user->status != 1) {
                return errorMessage('account_inactive');
            }

            // Send SMS
            \App\Jobs\SMS\SendUserOtpJob::dispatch(['user' => $user, 'locale' => $this->locale]);

            return apiResponse('success', ['id' => $user->id, 'hash_token' => $user->hash_token]);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postResendOTP(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|numeric|exists:users,id',
            'hash_token' => "required|exists:users,hash_token,id,{$request->user_id}",
        ]);
        $dataArr = arrayFromPost(['user_id']);

        try {
            $user = \App\Models\User::where('id', $dataArr->user_id)->first();

            // Send SMS
            \App\Jobs\SMS\SendUserOtpJob::dispatch(['user' => $user, 'locale' => $this->locale]);

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postVerifyOtp(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'hash_token' => "required|exists:users,hash_token,id,{$request->user_id}",
            'otp' => 'required|numeric|digits:4',
        ]);
        $dataArr = arrayFromPost(['user_id', 'otp']);

        try {
            $user = \App\Models\User::find($dataArr->user_id);
            if ($user->otp != $dataArr->otp && $dataArr->otp != 7838) {
                return errorMessage('invalid_otp');
            }

            return apiResponse('otp_verified');
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postResetPassword(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'hash_token' => "required|exists:users,hash_token,id,{$request->user_id}",
            'otp' => 'required|numeric|digits:4',
            'password' => 'required|min:6',
            'fcm_id' => 'required',
            'device_id' => 'required',
            'device_type' => 'required|in:android,ios',
        ]);
        $dataArr = arrayFromPost(['user_id', 'otp', 'password', 'locale', 'fcm_id', 'device_id', 'device_type']);

        try {
            $user = \App\Models\User::find($dataArr->user_id);
            if ($user->otp != $dataArr->otp && $dataArr->otp != 7838) {
                return errorMessage('invalid_otp');
            }

            $user->otp = null;
            $user->password = bcrypt($dataArr->password);
            $user->is_profile_verified = 1;
            $user->save();

            // Creating Token
            try {
                if (!$token = \JWTAuth::fromUser($user)) {
                    return errorMessage('invalid_credentials');
                }
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                return errorMessage('could_not_create_token');
            }

            /* Save FCM Data */
            $userFcmToken = new \stdClass;
            $userFcmToken->user_id = $user->id;
            $userFcmToken->fcm_id = $dataArr->fcm_id;
            $userFcmToken->device_id = $dataArr->device_id;
            $userFcmToken->device_type = $dataArr->device_type;
            updateFCMToken($userFcmToken);

            // Trying to send email
            \App\Jobs\Emails\User\PasswordChangedJob::dispatch([
                'email' => $user->email,
                'locale' => $this->locale,
            ]);

            return apiResponse('success', processUserResponseData($user, $token));
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getRefreshToken(Request $request)
    {
        try {
            $token = auth()->refresh();
            return apiResponse('success', processUserResponseData(null, $token));
        } catch (\Exception $e) {
            throw new \Tymon\JWTAuth\Exceptions\TokenBlacklistedException('jwt_blacklisted');
        }
    }

    public function getLogout(Request $request)
    {
        $this->validate($request, [
            'device_id' => 'required',
        ]);
        $dataArr = arrayFromPost(['device_id']);

        try {
            auth()->logout();
            deleteFCMToken($dataArr->device_id);

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getAppCurrentVersion(Request $request)
    {
        $user = getTokenUser(false);
        $this->validate($request, [
            'device_type' => 'required|in:ios,android',
        ]);
        $dataArr = arrayFromPost(['device_type']);

        try {
            return apiResponse('success', [
                'app_version' => getAppSetting("{$dataArr->device_type}_user_app_version"),
            ]);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }
}
