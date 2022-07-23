<?php

namespace App\Http\Controllers\API\User\V1;

use App\Http\Controllers\API\User\UserController;
use Illuminate\Http\Request;

class InquiryController extends UserController
{
    public function postSubmit(Request $request)
    {
        $user = getTokenUser(false);
        $this->validate($request, [
            'full_name' => 'required|max:250',
            'email' => 'required|email|max:250',
            'dial_code' => 'required|numeric|exists:countries,dial_code',
            'mobile' => 'required|numeric|digits_between:6,20',
            'message' => 'required|max:2000',
        ]);
        $dataArr = arrayFromPost(['full_name', 'email', 'dial_code', 'mobile', 'message']);

        try {
            $inquiry = new \App\Models\Inquiry();
            $inquiry->type = 1;
            $inquiry->user_id = $user ? $user->id : null;
            $inquiry->name = $dataArr->full_name;
            $inquiry->email = $dataArr->email;
            $inquiry->dial_code = $dataArr->dial_code;
            $inquiry->mobile = $dataArr->mobile;
            $inquiry->message = $dataArr->message;
            $inquiry->save();

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }
}
