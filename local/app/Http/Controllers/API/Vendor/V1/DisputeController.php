<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class DisputeController extends VendorController
{
    public function getList(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'page' => 'required|numeric|min:1',
        ]);

        try {
            $list = \App\Models\Dispute::select(\DB::raw('id, ticket_no, subject, status, last_message_by, created_at'))
                ->where('vendor_id', $vendor->id)
                ->orderBy('updated_at', 'DESC')
                ->paginate(10);

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getMessageList(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:disputes,id,vendor_id,{$vendor->id}",
            'page' => 'required|numeric|min:1',
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $list = \App\Models\DisputeDetail::select(\DB::raw('id, is_from_admin, message, attachment, created_at'))
                ->where('dispute_id', $dataArr->id)
                ->paginate(15);

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    // Raise Dispute
    public function postAdd(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'subject' => 'required|max:250',
            'message' => 'required|max:2000',
            'attachment' => 'nullable|' . config('cms.allowed_dispute_file_mimes'),
        ]);
        $dataArr = arrayFromPost(['subject', 'message']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $dispute = new \App\Models\Dispute();
            $dispute->vendor_id = $vendor->id;
            $dispute->ticket_no = 'TKT' . date('YmdHis');
            $dispute->subject = $dataArr->subject;
            $dispute->last_message_by = 3; //1.Admin, 2.User, 3.Vendor
            $dispute->save();

            // Add Message
            $disputeMessage = new \App\Models\DisputeDetail();
            $disputeMessage->dispute_id = $dispute->id;
            $disputeMessage->is_from_admin = 0;
            $disputeMessage->message = $dataArr->message;
            if ($request->attachment) {
                $dispute->attachment = uploadFile('attachment');
            }
            $disputeMessage->save();

            // Commit Transaction
            \DB::commit();

            return apiResponse('success', $dispute);
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function postMessage(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'dispute_id' => "required|numeric|exists:disputes,id,vendor_id,{$vendor->id}",
            'message' => 'required|max:2000',
            'attachment' => 'nullable|' . config('cms.allowed_dispute_file_mimes'),
        ]);
        $dataArr = arrayFromPost(['dispute_id', 'message']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            // Update Status
            $dispute = \App\Models\Dispute::find($dataArr->dispute_id);
            $dispute->last_message_by = 3; //1.Admin, 2.User, 3.Vendor
            $dispute->status = 2;
            $dispute->save();

            // Add Message
            $disputeMessage = new \App\Models\DisputeDetail();
            $disputeMessage->dispute_id = $dataArr->dispute_id;
            $disputeMessage->is_from_admin = 0;
            $disputeMessage->message = $dataArr->message;
            if ($request->attachment) {
                $disputeMessage->attachment = uploadFile('attachment');
            }
            $disputeMessage->save();

            // Commit Transaction
            \DB::commit();

            return apiResponse('success', $disputeMessage);
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }
}
