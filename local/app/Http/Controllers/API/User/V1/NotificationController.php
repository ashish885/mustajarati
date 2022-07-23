<?php

namespace App\Http\Controllers\API\User\V1;

use App\Http\Controllers\API\User\UserController;
use Illuminate\Http\Request;

class NotificationController extends UserController
{
    public function getListing(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'page' => 'required|numeric|min:1',
        ]);

        try {
            $notifications = \App\Models\Notification::select(\DB::raw("notifications.*, notifications.{$this->ql}title as title, notifications.{$this->ql}message as message"))
                ->where('notifications.user_id', '=', $user->id)
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
        $user = getTokenUser();
        $this->validate($request, [
            'id' => "required|exists:notifications,id,user_id,{$user->id}",
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
        $user = getTokenUser();
        try {
            \App\Models\Notification::where('user_id', $user->id)->delete();

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }
}
