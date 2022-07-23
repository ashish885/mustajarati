<?php

namespace App\Http\Controllers\API\User\V1;

use App\Http\Controllers\API\User\UserController;
use Illuminate\Http\Request;

class CityController extends UserController
{
    public function getList(Request $request)
    {
        $this->validate($request, [
            'country_id' => 'nullable|numeric|exists:countries,id',
            'term' => 'nullable',
        ]);
        $dataArr = arrayFromPost(['country_id', 'term']);

        try {
            $list = \App\Models\City::select(\DB::raw("id, {$this->ql}name as name, latitude, longitude"))
                ->where('status', 1)
                ->when(!blank($dataArr->country_id), function ($query) use ($dataArr) {
                    $query->where('country_id', $dataArr->country_id);
                })
                ->when(!blank($dataArr->term), function ($query) use ($dataArr) {
                    $query->where("{$this->ql}name", 'LIKE', "{$dataArr->term}%");
                })
                ->orderBy("{$this->ql}name")
                ->get();

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }
}
