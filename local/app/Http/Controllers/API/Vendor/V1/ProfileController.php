<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class ProfileController extends VendorController
{
    public function getDetails(Request $request)
    {
        $vendor = getTokenUser();

        try {
            return apiResponse('success', processVendorResponseData($vendor));
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postResubmitInfo(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'name' => 'required|max:250',
            'email' => "required|email",
            'dial_code' => 'required|numeric|exists:countries,dial_code',
            'mobile' => 'required|numeric|digits_between:6,20',
            'national_id' => 'required',
            'national_id_front_image' => 'nullable|' . config('cms.allowed_image_mimes'),
            'bank_id' => 'required|exists:banks,id',
            'account_no' => 'required',
            'iban_no' => 'required',
            'account_holder_name' => 'required',
        ]);
        $dataArr = arrayFromPost(['name', 'email', 'dial_code', 'mobile', 'national_id', 'bank_id', 'account_no', 'iban_no', 'account_holder_name']);

        try {
            if ($vendor->verification_status == 1) {
                return errorMessage('vendor_profile_already_approved');
            }

            // Start Transaction
            \DB::beginTransaction();

            // Check Mobile No Duplicate
            $dataArr->mobile = ltrim($dataArr->mobile, '0');
            if (\App\Models\Vendor::where('dial_code', $dataArr->dial_code)->where('mobile', $dataArr->mobile)->where('id', '<>', $vendor->id)->exists()) {
                return errorMessage('mobile_already_taken');
            }

            $vendor->name = $dataArr->name;
            $vendor->email = strtolower($dataArr->email);
            $vendor->dial_code = $dataArr->dial_code;
            $vendor->mobile = $dataArr->mobile;
            $vendor->national_id = $dataArr->national_id;
            if ($request->national_id_front_image) {
                $vendor->national_id_front_image = uploadFile('national_id_front_image');
            }
            if ($vendor->verification_status == 2) {
                $vendor->verification_status = 0;
                $vendor->rejection_message = null;
            }
            $vendor->save();

            // Bank Info
            $bankInfo = \App\Models\VendorBankDetail::where('vendor_id', $vendor->id)->first();
            if (blank($bankInfo)) {
                $bankInfo = new \App\Models\VendorBankDetail();
                $bankInfo->vendor_id = $vendor->id;
            }
            $bankInfo->bank_id = $dataArr->bank_id;
            $bankInfo->account_holder_name = $dataArr->account_holder_name;
            $bankInfo->account_no = $dataArr->account_no;
            $bankInfo->iban_no = $dataArr->iban_no;
            $bankInfo->save();

            // Commit Transaction
            \DB::commit();

            return apiResponse('success');
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function postUpdateProfile(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'name' => 'required|max:250',
            'email' => "required|email",
            'dial_code' => 'required|numeric|exists:countries,dial_code',
            'mobile' => 'required|numeric|digits_between:6,20',
        ]);
        $dataArr = arrayFromPost(['name', 'email', 'dial_code', 'mobile']);

        try {
            // Check Mobile No Duplicate
            $dataArr->mobile = ltrim($dataArr->mobile, '0');
            if (\App\Models\Vendor::where('dial_code', $dataArr->dial_code)->where('mobile', $dataArr->mobile)->where('id', '<>', $vendor->id)->exists()) {
                return errorMessage('mobile_already_taken');
            }

            $vendor->name = $dataArr->name;
            $vendor->email = strtolower($dataArr->email);
            $vendor->dial_code = $dataArr->dial_code;
            $vendor->mobile = $dataArr->mobile;
            $vendor->save();

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postUpdateImage(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'image' => config('cms.allowed_image_mimes'),
        ]);

        try {
            $vendor->profile_image = 'default-vendor.png';
            if ($request->image) {
                $vendor->profile_image = uploadFile('image');
                if (blank($vendor->profile_image)) {
                    return errorMessage('file_uploading_failed');
                }
            }
            $vendor->save();

            return apiResponse('success', ['image' => $vendor->profile_image]);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postUpdatePassword(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'old_password' => 'required|min:6',
            'password' => 'required|min:6',
        ]);
        $dataArr = arrayFromPost(['old_password', 'password']);

        try {
            if (!\Hash::check($dataArr->old_password, $vendor->password)) {
                return errorMessage('invalid_old_password');
            }

            $vendor->password = bcrypt($dataArr->password);
            $vendor->save();

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postUpdateFcmToken(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'fcm_id' => 'required',
            'device_id' => 'required',
            'device_type' => 'required|in:android,ios',
        ]);
        $dataArr = arrayFromPost(['fcm_id', 'device_id', 'device_type']);

        try {
            $fcmToken = new \stdClass;
            $fcmToken->vendor_id = $vendor->id;
            $fcmToken->fcm_id = $dataArr->fcm_id;
            $fcmToken->device_id = $dataArr->device_id;
            $fcmToken->device_type = $dataArr->device_type;
            updateFCMToken($fcmToken, 'vendor');

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    // Bank Info Fns
    public function getBankInfo(Request $request)
    {
        $vendor = getTokenUser();
        try {
            $bankInfo = \App\Models\VendorBankDetail::where('vendor_id', $vendor->id)->first();
            if (!blank($bankInfo)) {
                $bankInfo->bank_name = \App\Models\Bank::where('id', $bankInfo->bank_id)->value("{$this->ql}name");
            }

            return apiResponse('success', $bankInfo);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postBankInfoUpdate(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'bank_id' => 'required|exists:banks,id',
            'account_no' => 'required',
            'iban_no' => 'required',
            'account_holder_name' => 'required',
        ]);
        $dataArr = arrayFromPost(['bank_id', 'account_no', 'iban_no', 'account_holder_name']);

        try {
            $bankInfo = \App\Models\VendorBankDetail::where('vendor_id', $vendor->id)->first();
            if (blank($bankInfo)) {
                $bankInfo = new \App\Models\VendorBankDetail();
                $bankInfo->vendor_id = $vendor->id;
            }

            $bankInfo->bank_id = $dataArr->bank_id;
            $bankInfo->account_holder_name = $dataArr->account_holder_name;
            $bankInfo->account_no = $dataArr->account_no;
            $bankInfo->iban_no = $dataArr->iban_no;
            $bankInfo->save();

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    // Payment History Fns
    public function getPaymentHistory(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'page' => 'required|min:1',
        ]);
        $dataArr = arrayFromPost(['page']);

        try {
            $response = ['stats' => [], 'is_withdrawal_requested' => $vendor->is_withdrawal_requested];
            if ($dataArr->page == 1) {
                $response['stats']['total_amount'] = $vendor->total_amount;
                $response['stats']['total_paid_amount'] = $vendor->total_paid_amount;
                $response['stats']['total_pending_amount'] = $vendor->total_pending_amount;
            }

            $response['list'] = \App\Models\VendorPaymentHistory::select(\DB::raw('amount, transaction_id, comments, payment_date, created_at'))
                ->where('vendor_id', $vendor->id)
                ->paginate(15);

            return apiResponse('success', $response);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getWithdrawalRequest(Request $request)
    {
        $vendor = getTokenUser();
        try {
            if ($vendor->is_withdrawal_requested) {
                return errorMessage('already_withdrawal_requested');
            }

            $vendor->is_withdrawal_requested = 1;
            $vendor->withdrawal_request_date = date('Y-m-d H:i:s');
            $vendor->save();

            // Trying to send email
            \App\Jobs\Emails\Vendor\FundWithdrawalRequestJob::dispatch([
                'vendor' => $vendor,
                'locale' => $this->locale,
            ]);

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }
}
