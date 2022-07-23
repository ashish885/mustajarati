<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class InquiryController extends AdminController
{
    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.inquiries.users.index') || hasPermission('admin.inquiries.vendors.index'), 401);
        $type = $request->segment(3);

        return view('admin.inquiries.index', compact('type'));
    }

    public function getList(Request $request)
    {
        $list = \App\Models\Inquiry::select(\DB::raw("inquiries.*, users.name as user, vendors.name as vendor"))
            ->leftJoin('users', 'users.id', '=', 'inquiries.user_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'inquiries.vendor_id')
            ->where(function ($query) use ($request) {
                if ($request->type == 'users') {
                    $query->where('inquiries.type', 1);
                } else {
                    $query->where('inquiries.type', 2);
                }
            });

        return \DataTables::of($list)->make();
    }

    public function getDelete(Request $request)
    {
        abort_unless(hasPermission('admin.inquiries.delete'), 401);
        \App\Models\Inquiry::where('id', $request->id)->delete();
        return successMessage();
    }

    public function getView(Request $request)
    {
        $inquiry = \App\Models\Inquiry::find($request->id);
        $inquiry->user = $inquiry->user_id ? \App\Models\User::where('id', $inquiry->user_id)->value(\DB::raw('CONCAT(name, " (+", dial_code, mobile, ")")')) : '';
        $inquiry->vendor = $inquiry->vendor_id ? \App\Models\Vendor::where('id', $inquiry->vendor_id)->value(\DB::raw('CONCAT(name, " (+", dial_code, mobile, ")")')) : '';
        return view('admin.inquiries.view', compact('inquiry'));
    }

    public function getSendNotification(Request $request)
    {
        $result = \App\Models\Inquiry::find($request->id);
        return view('admin.inquiries.send-notification', compact('result'));
    }

    public function postSendNotification(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:250',
            'message' => 'required|max:2000',
        ]);
        $dataArr = arrayFromPost(['title', 'message']);

        try {
            $result = \App\Models\Inquiry::find($request->id);

            \App\Jobs\SendNotificationJob::dispatch([
                'title' => $dataArr->title,
                'en_title' => $dataArr->title,
                'message' => $dataArr->message,
                'en_message' => $dataArr->message,
                'attribute' => null,
                'value' => null,
                'notification_type' => 1,
            ], [
                'id' => $result->user_id ? $result->user_id : $result->vendor_id,
                'to' => $result->user_id ? 'user' : 'vendor',
            ]);

            return successMessage();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getSendEmail(Request $request)
    {
        $result = \App\Models\Inquiry::find($request->id);
        return view('admin.inquiries.send-email', compact('result'));
    }

    public function postSendEmail(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required|max:250',
            'message' => 'required|max:2000',
        ]);
        $dataArr = arrayFromPost(['subject', 'message']);

        try {
            $dataArr->to_email = \App\Models\Inquiry::where('id', $request->id)->value('email');

            \Mail::send([], [], function ($message) use ($dataArr) {
                $message->to($dataArr->to_email)
                    ->subject($dataArr->subject);
                $message->setBody($dataArr->message, 'text/html');
            });

            return successMessage('email_send_successfully');
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }
}
