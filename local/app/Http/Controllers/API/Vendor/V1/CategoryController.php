<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class CategoryController extends VendorController
{
    public function getList(Request $request)
    {
        $this->validate($request, [
            'parent_id' => 'nullable|numeric|exists:categories,id',
            'type' => 'required|in:product,service',
        ]);
        $dataArr = arrayFromPost(['parent_id', 'type']);

        try {
            $list = \App\Models\Category::select(\DB::raw("id, {$this->ql}name AS name"))
                ->where('type', ($dataArr->type == 'product' ? 1 : 2))
                ->when(!blank($dataArr->parent_id), function ($query) use ($dataArr) {
                    $query->where('parent_id', $dataArr->parent_id);
                })
                ->when(blank($dataArr->parent_id), function ($query) {
                    $query->whereNull('parent_id');
                })
                ->where('status', 1)
                ->get();

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);;
        }
    }
}
