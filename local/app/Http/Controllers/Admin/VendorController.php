<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class VendorController extends AdminController
{
    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.vendors.index'), 401);

        return view('admin.vendors.index');
    }

    public function getList(Request $request)
    {
        $vendors = \App\Models\Vendor::select(\DB::raw("vendors.*,vendor_bank_details.account_holder_name,vendor_bank_details.account_no"))
            ->leftJoin('vendor_bank_details', 'vendor_bank_details.vendor_id', '=', 'vendors.id')
            ->when(!blank($request->from_date) && !blank($request->to_date), function ($query) use ($request) {
                $query->whereBetween('vendors.created_at', [$request->from_date, $request->to_date]);
            });

        return \DataTables::of($vendors)
            ->addColumn('status_text', function ($query) {
                return transLang('action_status')[$query->status];
            })
            ->addColumn('verification_status_text', function ($query) {
                return transLang('verification_status_arr')[$query->verification_status];
            })
            ->addColumn('is_profile_editing_allowed_text', function ($query) {
                return transLang('other_action')[$query->is_profile_editing_allowed];
            })
            ->make();
    }

    public function getCreate(Request $request)
    {
        abort_unless(hasPermission('admin.vendors.create'), 401);

        $dial_codes = \App\Models\Country::select(\DB::raw("dial_code, CONCAT(dial_code, ' (', {$this->ql}name,')') AS name"))
            ->where('status', 1)
            ->orderBy('dial_code')
            ->get();

        $banks = \App\Models\Bank::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where('status', 1)
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.vendors.create', compact('dial_codes', 'dial_codes', 'banks'));
    }

    public function postCreate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:250',
            'email' => 'required|email|max:250',
            'dial_code' => 'required|numeric|exists:countries,dial_code',
            'mobile' => 'required|numeric|digits_between:6,20',
            'password' => 'required',
            'national_id' => 'required',
            'bank_code' => 'required',
            'bank_id' => 'required|exists:banks,id',
            'account_no' => 'required',
            'account_holder_name' => 'required',
            'national_id_image' => 'required|' . config('cms.allowed_image_mimes'),
            'profile_image' => 'nullable|' . config('cms.allowed_image_mimes'),
        ]);
        $dataArr = arrayFromPost(['name', 'email', 'dial_code', 'mobile', 'password', 'national_id', 'bank_id', 'account_no', 'account_holder_name', 'image', 'bank_code', 'status']);

        // Check Mobile No Duplicate
        $dataArr->mobile = ltrim($dataArr->mobile, '0');
        if (\App\Models\Vendor::where('dial_code', $dataArr->dial_code)->where('mobile', $dataArr->mobile)->exists()) {
            return errorMessage('mobile_already_taken');
        }

        try {
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
            $vendor->national_id_front_image = uploadFile('national_id_image');
            if ($request->profile_image) {
                $vendor->profile_image = uploadFile('profile_image');
            }
            $vendor->save();

            // Add Bank Details
            $vendorBankDetails = new \App\Models\VendorBankDetail();
            $vendorBankDetails->vendor_id = $vendor->id;
            $vendorBankDetails->bank_id = $dataArr->bank_id;
            $vendorBankDetails->account_holder_name = $dataArr->account_holder_name;
            $vendorBankDetails->account_no = $dataArr->account_no;
            $vendorBankDetails->iban_no = $dataArr->bank_code;
            $vendorBankDetails->save();
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
        abort_unless(hasPermission('admin.vendors.update'), 401);

        $vendor = \App\Models\Vendor::findOrFail($request->id);

        $vendor->bank_details = \App\Models\VendorBankDetail::where('vendor_id', $request->id)->first();

        $dial_codes = \App\Models\Country::select(\DB::raw("dial_code, CONCAT(dial_code, ' (', {$this->ql}name,')') AS name"))
            ->where('status', 1)
            ->orWhere('dial_code', $vendor->dial_code)
            ->orderBy('dial_code')
            ->get();

        $banks = \App\Models\Bank::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where('status', 1)
            ->orWhere('id', $vendor->bank_details->bank_id)
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.vendors.update', compact('vendor', 'dial_codes', 'banks'));
    }

    public function postUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:250',
            'email' => 'required|email|max:250',
            'dial_code' => 'required|numeric|exists:countries,dial_code',
            'mobile' => 'required|numeric|digits_between:6,20',
            'national_id' => 'required',
            'bank_code' => 'required',
            'bank_id' => 'required|exists:banks,id',
            'account_no' => 'required',
            'account_holder_name' => 'required',
            'national_id_image' => 'nullable|' . config('cms.allowed_image_mimes'),
            'profile_image' => 'nullable|' . config('cms.allowed_image_mimes'),
            'is_approved' => 'nullable',
        ]);
        $dataArr = arrayFromPost(['name', 'email', 'dial_code', 'mobile', 'password', 'national_id', 'bank_id', 'account_no', 'account_holder_name', 'bank_code', 'status', 'is_approved']);

        // Check Mobile No Duplicate
        $dataArr->mobile = ltrim($dataArr->mobile, '0');
        if (\App\Models\Vendor::where('dial_code', $dataArr->dial_code)->where('mobile', $dataArr->mobile)->where('id', '<>', $request->id)->exists()) {
            return errorMessage('mobile_already_taken');
        }

        try {
            // Start Transaction
            \DB::beginTransaction();

            $vendor = \App\Models\Vendor::find($request->id);
            $vendor->name = $dataArr->name;
            $vendor->email = strtolower($dataArr->email);
            $vendor->dial_code = $dataArr->dial_code;
            $vendor->mobile = $dataArr->mobile;
            $vendor->hash_token = generateRandomString(25);
            $vendor->national_id = $dataArr->national_id;
            if ($request->national_id_image) {
                $vendor->national_id_front_image = uploadFile('national_id_image');
            }
            if ($request->profile_image) {
                $vendor->profile_image = uploadFile('profile_image');
            }
            $vendor->save();

            // Add Bank Details
            $vendorBankDetails = \App\Models\VendorBankDetail::where('vendor_id', $vendor->id)->first();
            if (blank($vendorBankDetails)) {
                $vendorBankDetails = new \App\Models\VendorBankDetail();
                $vendorBankDetails->vendor_id = $vendor->id;
            }
            $vendorBankDetails->bank_id = $dataArr->bank_id;
            $vendorBankDetails->account_holder_name = $dataArr->account_holder_name;
            $vendorBankDetails->account_no = $dataArr->account_no;
            $vendorBankDetails->iban_no = $dataArr->bank_code;
            $vendorBankDetails->save();

            if ($dataArr->is_approved) {
                $vendor->verification_status = 1;
                $vendor->save();

                \App\Jobs\Notifications\Vendor\ApplicationApprovedJob::dispatch(['vendor_id' => $vendor->id]);
            }

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
        abort_unless(hasPermission('admin.vendors.delete'), 401);

        \App\Models\Vendor::where('id', $request->id)->delete();
        return successMessage();
    }

    public function getView(Request $request)
    {
        abort_unless(hasPermission('admin.vendors.view'), 401);

        $vendor = \App\Models\Vendor::findOrFail($request->id);
        $vendor->bank_details = \App\Models\VendorBankDetail::where('vendor_id', $request->id)->first();
        if ($vendor->bank_details) {
            $vendor->bank_details->bank_name = \App\Models\Bank::where('id', $vendor->bank_details->bank_id)->value("{$this->ql}name");
        }

        if ($vendor->verification_status == 1) {
            return view('admin.vendors.view', compact('vendor'));
        } else {
            $dial_codes = \App\Models\Country::select(\DB::raw("dial_code, CONCAT(dial_code, ' (', {$this->ql}name,')') AS name"))
                ->where('status', 1)
                ->orderBy('dial_code')
                ->get();

            $banks = \App\Models\Bank::select(\DB::raw("id, {$this->ql}name AS name"))
                ->where('status', 1)
                ->orderBy("{$this->ql}name")
                ->get();

            return view('admin.vendors.verify_reject_application', compact('vendor', 'dial_codes', 'banks'));
        }
    }

    public function getRejectApplication(Request $request)
    {
        $id = $request->id;
        return view('admin.vendors.reject_application', compact('id'));
    }

    public function postRejectApplication(Request $request)
    {
        $this->validate($request, [
            'comments' => 'required|max:2000',
            'allow_profile_editing' => 'required',
        ]);
        $dataArr = arrayFromPost(['comments', 'allow_profile_editing']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $vendor = \App\Models\Vendor::find($request->id);
            if (!blank($vendor)) {
                $vendor->verification_status = 2;
                $vendor->rejection_message = $dataArr->comments;
                $vendor->is_profile_editing_allowed = $dataArr->allow_profile_editing;
                $vendor->save();

                \App\Jobs\Notifications\Vendor\ApplicationRejectedJob::dispatch(['vendor_id' => $vendor->id, 'comments' => $dataArr->comments]);
            }

            // Commit Transaction
            \DB::commit();

            return successMessage();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($th);
        }
    }

    public function getPasswordReset(Request $request)
    {
        abort_unless(hasPermission('admin.vendors.reset_password'), 401);
        $id = $request->id;
        return view('admin.vendors.reset_password', compact('id'));
    }

    public function postPasswordReset(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            $vendor = \App\Models\Vendor::find($request->id);
            $vendor->password = bcrypt($request->password);
            $vendor->save();

            return successMessage('password_changed');
            dd($vendor);
        } catch (\Throwable $th) {
            return exceptionErrorMessage($th);
        }
    }

    // Payment History Fns
    public function getPaymentStats(Request $request)
    {
        $vendor = \App\Models\Vendor::findOrFail($request->id);
        $total_amount = $vendor->total_amount;
        $total_paid_amount = $vendor->total_paid_amount;
        $total_pending_amount = $vendor->total_pending_amount;

        return response()->json(compact('total_amount', 'total_paid_amount', 'total_pending_amount'));
    }

    public function getPaymentHistoryList(Request $request)
    {
        $list = \App\Models\VendorPaymentHistory::where('vendor_id', $request->id);

        return \DataTables::of($list)->make();
    }

    public function getPaymentHistoryCreate(Request $request)
    {
        abort_unless(hasPermission('admin.vendors.payment.create'), 401);

        $id = $request->id;
        return view('admin.vendors.payment.create', compact('id'));
    }

    public function postPaymentHistoryCreate(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric|gt:0',
            'transaction_id' => 'required|max:200',
            'payment_date' => 'required|max:2000',
            'comments' => 'nullable',
            'attachment' => 'nullable|mimes:pdf,jpeg,png,jpg|max:2096',
        ]);
        $dataArr = arrayFromPost(['amount', 'transaction_id', 'payment_date', 'comments']);

        try {
            $vendor = \App\Models\Vendor::find($request->id);
            if ($vendor->total_pending_amount < $dataArr->amount) {
                return errorMessage(transLang('vendor_pending_amount_error', ['amount' => $vendor->total_pending_amount]), true);
            }

            // Start Transaction
            \DB::beginTransaction();

            $paymentHistory = new \App\Models\VendorPaymentHistory();
            $paymentHistory->vendor_id = $request->id;
            $paymentHistory->amount = $dataArr->amount;
            $paymentHistory->transaction_id = $dataArr->transaction_id;
            $paymentHistory->payment_date = $dataArr->payment_date;
            $paymentHistory->comments = $dataArr->comments;
            if ($request->attachment) {
                $paymentHistory->attachment = uploadFile('attachment');
            }
            $paymentHistory->save();

            $vendor = \App\Models\Vendor::find($request->id);
            $vendor->total_pending_amount -= $paymentHistory->amount;
            $vendor->total_paid_amount += $paymentHistory->amount;
            $vendor->is_withdrawal_requested = 0;
            $vendor->withdrawal_request_date = null;
            $vendor->save();

            // Commit Transaction
            \DB::commit();

            return successMessage();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return errorMessage($e->getMessage(), true);
        }
    }
}
