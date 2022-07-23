<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class NotificationController extends AdminController
{
    public function getIndex()
    {
        return view('admin.notification.index');
    }

    public function getUsersList(Request $request)
    {
        $output = new \stdClass();
        $output->incomplete_results = false;
        $output->items = array();
        $output->total_count = 0;

        $list = \App\Models\User::select(\DB::raw('id, CONCAT(name, " (+", dial_code, " ", mobile, ")") AS name'));
        if (!blank($request->term)) {
            $list->where('name', 'like', "%{$request->term}%")
                ->orWhere('email', 'like', "%{$request->term}%")
                ->orWhere('mobile', 'like', "%{$request->term}%")
                ->orWhere('dial_code', 'like', "%{$request->term}%");
        }
        $list = $list->paginate(10)
            ->toArray();

        $response = [
            'incomplete_results' => false,
            'items' => $list['data'],
            'total_count' => count($list['data']),
        ];

        return response()->json($response);
    }

    public function getVendorsList(Request $request)
    {
        $output = new \stdClass();
        $output->incomplete_results = false;
        $output->items = array();
        $output->total_count = 0;

        $list = \App\Models\Vendor::select(\DB::raw("id, CONCAT(name, ' (', dial_code, mobile, ')') AS name"));
        if (!blank($request->term)) {
            $list->where('name', 'like', "%{$request->term}%")
                ->orWhere(\DB::raw('CONCAT(dial_code, mobile)'), 'like', "%{$request->term}%");
        }
        $list = $list->paginate(10)
            ->toArray();

        $response = [
            'incomplete_results' => false,
            'items' => $list['data'],
            'total_count' => count($list['data']),
        ];

        return response()->json($response);
    }

    public function postSend(Request $request)
    {
        $this->validate($request, [
            'send_to' => 'required',
            'title' => 'required',
            'en_title' => 'required',
            'ar_message' => 'required',
            'en_message' => 'required',
            'user_id' => 'nullable|array',
            'vendor_id' => 'nullable|array',
        ]);
        $dataArr = arrayFromPost(['send_to', 'title', 'en_title', 'ar_message', 'en_message', 'user_id', 'vendor_id']);

        try {
            $send_to = $dataArr->send_to;
            $idsArr = $send_to == 'user' ? $dataArr->user_id : $dataArr->vendor_id;

            if ($send_to == 'user' && !(is_array($dataArr->user_id) && count($dataArr->user_id))) {
                $idsArr = \App\Models\User::pluck('id')->toArray();
            }
            if ($send_to == 'vendor' && !(is_array($dataArr->vendor_id) && count($dataArr->vendor_id))) {
                $idsArr = \App\Models\Vendor::pluck('id')->toArray();
            }

            \App\Jobs\SendNotificationJob::dispatch([
                'title' => $dataArr->title,
                'en_title' => $dataArr->en_title,
                'message' => $dataArr->ar_message,
                'en_message' => $dataArr->en_message,
                'attribute' => null,
                'value' => null,
                'notification_type' => 1,
            ], [
                'id' => $idsArr,
                'to' => $send_to
            ]);

            return successMessage();
        } catch (\Exception $e) {
            return errorMessage($e->getMessage(), true);
        }
    }
}
