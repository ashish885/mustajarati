<?php

namespace App\Http\Controllers\API\User\V1;

use App\Http\Controllers\API\User\UserController;
use Illuminate\Http\Request;

class DisputeController extends UserController
{
    public function getList(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'page' => 'required|numeric|min:1',
        ]);

        try {
            $list = \App\Models\Dispute::select(\DB::raw('id, ticket_no, subject, status, last_message_by, created_at'))
                ->where('user_id', $user->id)
                ->orderBy('updated_at', 'DESC')
                ->paginate(10);

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getMessageList(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:disputes,id,user_id,{$user->id}",
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
        $user = getTokenUser();
        $this->validate($request, [
            'subject' => 'required|max:250',
            'message' => 'required|max:2000',
            'attachment' => 'nullable|' . config('cms.allowed_dispute_file_mimes'),
            'type' => 'nullable|in:service:product',
            'id' => 'nullable|numeric|' . ($request->type == 'service' ? 'exists:service_bookings' : 'exists:product_bookings'),
        ]);
        $dataArr = arrayFromPost(['subject', 'message', 'type', 'id']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $dispute = new \App\Models\Dispute();
            $dispute->user_id = $user->id;
            $dispute->ticket_no = 'TKT' . date('YmdHis');
            $dispute->subject = $dataArr->subject;
            $dispute->last_message_by = 2; //1.Admin, 2.User, 3.Vendor
            if ($dataArr->type && $dataArr->id) {
                $dispute->{"{$dataArr->type}_booking_id"} = $dataArr->id;
            }
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

            // Trying to send email
            \App\Jobs\Emails\User\DisputeRaisedJob::dispatch([
                'email' => $user->email,
                'locale' => $this->locale,
            ]);

            // Trying to send email
            if ($dataArr->type && $dataArr->id) {
                $booking_code = $dataArr->type == 'service' ? \App\Models\ServiceBooking::where('id', $dataArr->id)->value('booking_code') : \App\Models\ProductBooking::where('id', $dataArr->id)->value('booking_code');

                \App\Jobs\Emails\Vendor\CustomerRaisedDisputeJob::dispatch([
                    'booking_code' => $booking_code,
                    'email' => $user->email,
                    'locale' => $this->locale,
                ]);
            }

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
        $user = getTokenUser();
        $this->validate($request, [
            'dispute_id' => "required|numeric|exists:disputes,id,user_id,{$user->id}",
            'message' => 'required|max:2000',
            'attachment' => 'nullable|' . config('cms.allowed_dispute_file_mimes'),
        ]);
        $dataArr = arrayFromPost(['dispute_id', 'message']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            // Update Status
            $dispute = \App\Models\Dispute::find($dataArr->dispute_id);
            $dispute->last_message_by = 2; //1.Admin, 2.User, 3.Vendor
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
