<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class DisputeController extends AdminController
{
    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.disputes.index'), 401);

        $vendors = \App\Models\Vendor::select(\DB::raw('id, CONCAT(name, " (", email, ")") AS name'))
            ->where('is_profile_verified', 1)
            ->orderBy("name")
            ->get();

        $users = \App\Models\User::select(\DB::raw('id, CONCAT(name, " (","+",dial_code,mobile, ")") AS name'))
            ->where('is_profile_verified', 1)
            ->orderBy("name")
            ->get();

        return view('admin.disputes.index', compact('users', 'vendors'));
    }

    public function getList(Request $request)
    {
        $list = \App\Models\Dispute::select(\DB::raw("disputes.*, users.name as user, vendors.name as vendor"))
            ->leftJoin('users', 'users.id', '=', 'disputes.user_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'disputes.vendor_id')
            ->when(!blank($request->user_id), function ($query) use ($request) {
                $query->where('disputes.user_id', $request->user_id);
            })
            ->when(!blank($request->vendor_id), function ($query) use ($request) {
                $query->where('disputes.vendor_id', $request->vendor_id);
            })
            ->when(!blank($request->ticket_status), function ($query) use ($request) {
                if ($request->ticket_status == 3) {
                    $query->where('disputes.status', 3);
                } elseif ($request->ticket_status == 2) {
                    $query->where('last_message_by', '<>', 1)->where('disputes.status', '<', 3);
                } else {
                    $query->where('last_message_by', 1)->where('disputes.status', '<', 3);
                }
            });

        return \DataTables::of($list)
            ->addColumn('dispute_status', function ($query) {
                return $query->status == 3 ? transLang('dispute_status_arr')[3] : ($query->last_message_by == 1 ? transLang('dispute_status_arr')[2] : transLang('dispute_status_arr')[1]);
            })
            ->make();
    }


    public function getDelete(Request $request)
    {
        abort_unless(hasPermission('admin.disputes.delete'), 401);

        \App\Models\Dispute::where('id', $request->id)->delete();
        return successMessage();
    }

    public function getView(Request $request)
    {
        abort_unless(hasPermission('admin.disputes.view'), 401);

        $dispute =  \App\Models\Dispute::findOrFail($request->id);
        $dispute->raised_by = $dispute->user_id ? 1 : 2;
        $dispute->posted_by = $dispute->user_id ? \App\Models\User::where('id', $dispute->user_id)->value(\DB::raw('CONCAT(name, " (+", dial_code, mobile, ")")')) : \App\Models\Vendor::where('id', $dispute->vendor_id)->value(\DB::raw('CONCAT(name, " (+", dial_code, mobile, ")")'));
        $dispute->product_booking_code = $dispute->product_booking_id ? \App\Models\ProductBooking::where('id', $dispute->product_booking_id)->value('booking_code') : '';
        $dispute->service_booking_code = $dispute->service_booking_id ? \App\Models\ServiceBooking::where('id', $dispute->service_booking_id)->value('booking_code') : '';

        $dispute->details = \App\Models\DisputeDetail::where('dispute_id', $dispute->id)
            ->get();

        return view('admin.disputes.view', compact('dispute'));
    }

    public function getPostReply(Request $request)
    {
        $id = $request->id;
        return view('admin.disputes.reply', compact('id'));
    }

    public function postPostReply(Request $request)
    {
        $this->validate($request, [
            'message' => 'required|max:2000',
            'attachment' => 'nullable|' . config('cms.allowed_dispute_file_mimes'),
        ]);
        $dataArr = arrayFromPost(['dispute_id', 'message']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            // Update Status
            $dispute = \App\Models\Dispute::find($request->id);
            $dispute->last_message_by = 1; //1.Admin, 2.User, 3.Vendor
            $dispute->status = 2;
            $dispute->save();

            // Add Message
            $disputeMessage = new \App\Models\DisputeDetail();
            $disputeMessage->dispute_id = $dispute->id;
            $disputeMessage->is_from_admin = 1;
            $disputeMessage->message = $dataArr->message;
            if ($request->attachment) {
                $disputeMessage->attachment = uploadFile('attachment');
            }
            $disputeMessage->save();

            // Commit Transaction
            \DB::commit();

            return successMessage();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function getCloseTicket(Request $request)
    {
        $dispute = \App\Models\Dispute::find($request->id);
        if (!blank($dispute)) {
            $dispute->status = 3;
            $dispute->save();
        }
        return successMessage();
    }
}
