<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class NotificationController extends VendorController
{
    public function getListing(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'page' => 'required|numeric|min:1',
        ]);

        try {
            $notifications = \App\Models\Notification::select(\DB::raw("notifications.*, notifications.{$this->ql}title as title, notifications.{$this->ql}message as message"))
                ->where('notifications.vendor_id', '=', $vendor->id)
                ->orderBy('notifications.id', 'desc')
                ->paginate(20);
            if ($notifications->count()) {
                \App\Models\Notification::whereIn('id', $notifications->pluck('id')->toArray())
                    ->update(['is_read' => 1]);
            }

            return apiResponse('success', $notifications);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }

    public function getDelete(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|exists:notifications,id,vendor_id,{$vendor->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            \App\Models\Notification::where('id', $dataArr->id)->delete();

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }

    public function getDeleteAll(Request $request)
    {
        $vendor = getTokenUser();
        try {
            \App\Models\Notification::where('vendor_id', $vendor->id)->delete();

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }
}
