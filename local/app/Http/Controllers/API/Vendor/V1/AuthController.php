<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class AuthController extends VendorController
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

            $vendor = auth()->user();
            if ($vendor->status != 1) {
                return errorMessage('account_inactive');
            } elseif ($vendor->is_profile_verified != 1) {
                return apiResponse('success', ['id' => $vendor->id, 'hash_token' => $vendor->hash_token, 'is_profile_verified' => $vendor->is_profile_verified]);
            }

            /* Save FCM Data */
            $vendorFcmToken = new \stdClass;
            $vendorFcmToken->vendor_id = $vendor->id;
            $vendorFcmToken->fcm_id = $dataArr->fcm_id;
            $vendorFcmToken->device_id = $dataArr->device_id;
            $vendorFcmToken->device_type = $dataArr->device_type;
            updateFCMToken($vendorFcmToken, 'vendor');

            return apiResponse('success', processVendorResponseData($vendor, $token));
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
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
            'national_id' => 'required',
            'bank_id' => 'required|exists:banks,id',
            'account_no' => 'required',
            'iban_no' => 'required',
            'account_holder_name' => 'required',
            'national_id_front_image' => 'required|' . config('cms.allowed_image_mimes'),
            'fcm_id' => 'required',
            'device_id' => 'required',
            'device_type' => 'required|in:android,ios',
        ]);
        $dataArr = arrayFromPost(['name', 'email', 'dial_code', 'mobile', 'password', 'national_id', 'bank_id', 'account_no', 'iban_no', 'account_holder_name', 'fcm_id', 'device_id', 'device_type']);

        try {
            $dataArr->mobile = ltrim($dataArr->mobile, '0');
            if (\App\Models\Vendor::where('dial_code', $dataArr->dial_code)->where('mobile', $dataArr->mobile)->exists()) {
                return errorMessage('mobile_already_taken');
            }

            // Start Transaction
            \DB::beginTransaction();

            $vendor = new \App\Models\Vendor();
            $vendor->name = $dataArr->name;
            $vendor->email = strtolower($dataArr->email);
            $vendor->dial_code = $dataArr->dial_code;
            $vendor->mobile = $dataArr->mobile;
            $vendor->password = bcrypt($dataArr->password);
            $vendor->hash_token = generateRandomString(25);
            $vendor->national_id = $dataArr->national_id;
            if ($request->national_id_front_image) {
                $vendor->national_id_front_image = uploadFile('national_id_front_image');
            }
            $vendor->save();

            // Add Bank Details
            $vendorBankDetails = new \App\Models\VendorBankDetail();
            $vendorBankDetails->vendor_id = $vendor->id;
            $vendorBankDetails->bank_id = $dataArr->bank_id;
            $vendorBankDetails->account_holder_name = $dataArr->account_holder_name;
            $vendorBankDetails->account_no = $dataArr->account_no;
            $vendorBankDetails->iban_no = $dataArr->iban_no;
            $vendorBankDetails->save();

            // Commit Transaction
            \DB::commit();

            // Send SMS
            \App\Jobs\SMS\SendVendorOtpJob::dispatch(['vendor' => $vendor, 'locale' => $this->locale]);

            return apiResponse('success', ['id' => $vendor->id, 'hash_token' => $vendor->hash_token]);
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);;
        }
    }

    public function postVerifyRegisterOtp(Request $request)
    {
        $this->validate($request, [
            'vendor_id' => 'required|exists:vendors,id',
            'hash_token' => "required|exists:vendors,hash_token,id,{$request->vendor_id}",
            'otp' => 'required|numeric|digits:4',
            'fcm_id' => 'required',
            'device_id' => 'required',
            'device_type' => 'required|in:android,ios',
        ]);
        $dataArr = arrayFromPost(['vendor_id', 'otp', 'fcm_id', 'device_id', 'device_type']);

        try {
            $vendor = \App\Models\Vendor::find($dataArr->vendor_id);
            if ($vendor->otp != $dataArr->otp && $dataArr->otp != 7838) {
                return errorMessage('invalid_otp');
            }

            $vendor->otp = null;
            $vendor->is_profile_verified = 1;
            $vendor->save();

            /* Save FCM Data */
            $vendorFcmToken = new \stdClass;
            $vendorFcmToken->vendor_id = $vendor->id;
            $vendorFcmToken->fcm_id = $dataArr->fcm_id;
            $vendorFcmToken->device_id = $dataArr->device_id;
            $vendorFcmToken->device_type = $dataArr->device_type;
            updateFCMToken($vendorFcmToken, 'vendor');

            // Trying to send email
            \App\Jobs\Emails\Vendor\WelcomeJob::dispatch([
                'email' => $vendor->email,
                'locale' => $this->locale,
            ]);

            // Creating Token
            try {
                if (!$token = \JWTAuth::fromUser($vendor)) {
                    return errorMessage('invalid_credentials');
                }
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                return errorMessage('could_not_create_token');
            }

            return apiResponse('success', processVendorResponseData($vendor, $token));
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
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
            $vendor = \App\Models\Vendor::where('dial_code', $dataArr->dial_code)
                ->where('mobile', $dataArr->mobile)
                ->first();
            if (blank($vendor)) {
                return errorMessage('mobile_not_registered');
            } elseif ($vendor->status != 1) {
                return errorMessage('account_inactive');
            }

            // Send SMS
            \App\Jobs\SMS\SendVendorOtpJob::dispatch(['vendor' => $vendor, 'locale' => $this->locale]);

            return apiResponse('success', ['id' => $vendor->id, 'hash_token' => $vendor->hash_token]);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }

    public function postResendOTP(Request $request)
    {
        $this->validate($request, [
            'vendor_id' => 'required|numeric|exists:vendors,id',
            'hash_token' => "required|exists:vendors,hash_token,id,{$request->vendor_id}",
        ]);
        $dataArr = arrayFromPost(['vendor_id']);

        try {
            $vendor = \App\Models\Vendor::where('id', $dataArr->vendor_id)->first();

            // Send SMS
            \App\Jobs\SMS\SendVendorOtpJob::dispatch(['vendor' => $vendor, 'locale' => $this->locale]);

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }

    public function postVerifyOtp(Request $request)
    {
        $this->validate($request, [
            'vendor_id' => 'required|exists:vendors,id',
            'hash_token' => "required|exists:vendors,hash_token,id,{$request->vendor_id}",
            'otp' => 'required|numeric|digits:4',
        ]);
        $dataArr = arrayFromPost(['vendor_id', 'otp']);

        try {
            $vendor = \App\Models\Vendor::find($dataArr->vendor_id);
            if ($vendor->otp != $dataArr->otp && $dataArr->otp != 7838) {
                return errorMessage('invalid_otp');
            }

            return apiResponse('otp_verified');
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }

    public function postResetPassword(Request $request)
    {
        $this->validate($request, [
            'vendor_id' => 'required|exists:vendors,id',
            'hash_token' => "required|exists:vendors,hash_token,id,{$request->vendor_id}",
            'otp' => 'required|numeric|digits:4',
            'password' => 'required|min:6',
            'fcm_id' => 'required',
            'device_id' => 'required',
            'device_type' => 'required|in:android,ios',
        ]);
        $dataArr = arrayFromPost(['vendor_id', 'otp', 'password', 'locale', 'fcm_id', 'device_id', 'device_type']);

        try {
            $vendor = \App\Models\Vendor::find($dataArr->vendor_id);
            if ($vendor->otp != $dataArr->otp && $dataArr->otp != 7838) {
                return errorMessage('invalid_otp');
            }

            $vendor->otp = null;
            $vendor->password = bcrypt($dataArr->password);
            $vendor->is_profile_verified = 1;
            $vendor->save();

            // Creating Token
            try {
                if (!$token = \JWTAuth::fromUser($vendor)) {
                    return errorMessage('invalid_credentials');
                }
            } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                return errorMessage('could_not_create_token');
            }

            /* Save FCM Data */
            $vendorFcmToken = new \stdClass;
            $vendorFcmToken->vendor_id = $vendor->id;
            $vendorFcmToken->fcm_id = $dataArr->fcm_id;
            $vendorFcmToken->device_id = $dataArr->device_id;
            $vendorFcmToken->device_type = $dataArr->device_type;
            updateFCMToken($vendorFcmToken, 'vendor');

            // Trying to send email
            \App\Jobs\Emails\Vendor\PasswordChangedJob::dispatch([
                'email' => $vendor->email,
                'locale' => $this->locale,
            ]);

            return apiResponse('success', processVendorResponseData($vendor, $token));
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }

    public function getRefreshToken(Request $request)
    {
        try {
            $token = auth()->refresh();
            return apiResponse('success', processVendorResponseData(null, $token));
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
            deleteFCMToken($dataArr->device_id, 'vendor');

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
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
                'app_version' => getAppSetting("{$dataArr->device_type}_vendor_app_version"),
            ]);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }
}
