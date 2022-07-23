<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class CountryController extends VendorController
{
    public function getList(Request $request)
    {
        try {
            $list = \App\Models\Country::select(\DB::raw("{$this->ql}name as name, dial_code, flag"))
                ->where('status', 1)
                ->orderBy("{$this->ql}name")
                ->get();

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }
}
