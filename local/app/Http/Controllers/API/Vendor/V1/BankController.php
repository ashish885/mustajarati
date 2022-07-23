<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class BankController extends VendorController
{
    public function getList(Request $request)
    {
        try {
            $list = \App\Models\Bank::select(\DB::raw("id, {$this->ql}name AS name"))
                ->where('status', 1)
                ->get();

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }
}
