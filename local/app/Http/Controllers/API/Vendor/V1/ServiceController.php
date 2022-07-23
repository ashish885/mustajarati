<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class ServiceController extends VendorController
{
    public function getList(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'page' => 'required|numeric|min:1',
        ]);

        try {
            $list = \App\Models\Service::select(\DB::raw("services.id, services.default_image, services.{$this->ql}name AS name, services.total_bookings, services.total_ratings, services.avg_rating, services.daily_amount, categories.{$this->ql}name AS category, sub_categories.{$this->ql}name AS sub_category, services.amount, services.amount_type, services.is_sponsored"))
                ->join('categories', 'categories.id', '=', 'services.category_id')
                ->join('categories AS sub_categories', 'sub_categories.id', '=', 'services.sub_category_id')
                ->where('services.vendor_id', $vendor->id)
                ->orderBy('services.id', 'DESC')
                ->paginate(10);

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getDetails(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:services,id,vendor_id,{$vendor->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $service = \App\Models\Service::find($dataArr->id);
            if (!blank($service)) {
                $service->category = \App\Models\Category::where('id', $service->category_id)->value("{$this->ql}name");
                $service->sub_category = \App\Models\Category::where('id', $service->sub_category_id)->value("{$this->ql}name");

                $service->images = \App\Models\ServiceImage::where('service_id', $service->id)
                    ->orderBy('priority')
                    ->pluck('image');

                $service->cities = \App\Models\City::select(\DB::raw("cities.id, cities.{$this->ql}name as name"))
                    ->join('service_cities', 'service_cities.city_id', '=', 'cities.id')
                    ->where('service_cities.service_id', $service->id)
                    ->get();
            }

            return apiResponse('success', $service);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postAdd(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'type' => 'required|in:0,1',
            'category_id' => 'required|numeric|exists:categories,id',
            'sub_category_id' => "required|numeric|exists:categories,id,parent_id,{$request->category_id}",
            'name' => 'required|max:250',
            'description' => 'required|max:2000',
            'service_amount' => 'required|numeric|gt:0',
            'service_amount_type' => 'required|numeric|in:1,2,3', // 1.Hourly, 2.Daily, 3.Monthly
            'visiting_charges' => ($request->type == 1 ? 'required|numeric|gte:0|lte:5000' : 'nullable|numeric|gte:0|lte:5000'),
            'cities' => 'required|array',
            'cities.*' => 'required|distinct|numeric|exists:cities,id',
            'address' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images' => 'nullable|array|max:5',
            'images.*' => 'required',
        ]);
        $dataArr = arrayFromPost(['type', 'category_id', 'sub_category_id', 'name', 'description', 'service_amount', 'service_amount_type', 'visiting_charges', 'cities', 'address', 'latitude', 'longitude', 'images']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $service = new \App\Models\Service();
            $service->vendor_id = $vendor->id;
            $service->type = $dataArr->type;
            $service->category_id = $dataArr->category_id;
            $service->sub_category_id = $dataArr->sub_category_id;
            $service->default_image = @$dataArr->images[0];
            $service->name = $dataArr->name;
            $service->en_name = $dataArr->name;
            $service->description = $dataArr->description;
            $service->en_description = $dataArr->description;
            $service->amount = $dataArr->service_amount;
            $service->amount_type = $dataArr->service_amount_type;
            $service->visiting_charges = $dataArr->type == 1 ? $dataArr->visiting_charges : 0;
            $service->address = $dataArr->address;
            $service->latitude = $dataArr->latitude;
            $service->longitude = $dataArr->longitude;

            // Calculate Daily Amount
            if ($service->amount_type == 1) { // Hourly
                $service->daily_amount = $service->amount * 24;

            } elseif ($service->amount_type == 2) { // Daily
                $service->daily_amount = $service->amount;

            } elseif ($service->amount_type == 3) { // Monthly
                $service->daily_amount = $service->amount / 30;
            }
            $service->save();

            // Link Cities
            if (is_array($dataArr->cities) && count($dataArr->cities)) {
                foreach ($dataArr->cities as $city_id) {
                    $serviceCity = new \App\Models\ServiceCity();
                    $serviceCity->service_id = $service->id;
                    $serviceCity->city_id = $city_id;
                    $serviceCity->save();
                }
            }

            // Link Images
            if (is_array($dataArr->images) && count($dataArr->images)) {
                foreach ($dataArr->images as $key => $image) {
                    $serviceImage = new \App\Models\ServiceImage();
                    $serviceImage->service_id = $service->id;
                    $serviceImage->image = $image;
                    $serviceImage->priority = $key;
                    $serviceImage->save();
                }
            }

            // Commit Transaction
            \DB::commit();

            return apiResponse();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function postUpdate(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:services,id,vendor_id,{$vendor->id}",
            'type' => 'required|in:0,1',
            'category_id' => 'required|numeric|exists:categories,id',
            'sub_category_id' => "required|numeric|exists:categories,id,parent_id,{$request->category_id}",
            'name' => 'required|max:250',
            'description' => 'required|max:2000',
            'service_amount' => 'required|numeric|gt:0',
            'service_amount_type' => 'required|numeric|in:1,2,3', // 1.Hourly, 2.Daily, 3.Monthly
            'visiting_charges' => ($request->type == 1 ? 'required|numeric|gte:0|lte:5000' : 'nullable|numeric|gte:0|lte:5000'),
            'cities' => 'required|array',
            'cities.*' => 'required|distinct|numeric|exists:cities,id',
            'address' => 'nullable',
            'latitude' => ($request->address ? 'required|numeric' : 'nullable|numeric'),
            'longitude' => ($request->address ? 'required|numeric' : 'nullable|numeric'),
            'images' => 'nullable|array|max:5',
            'images.*' => 'required',
        ]);
        $dataArr = arrayFromPost(['id', 'type', 'category_id', 'sub_category_id', 'name', 'description', 'service_amount', 'service_amount_type', 'visiting_charges', 'cities', 'address', 'latitude', 'longitude', 'images']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $service = \App\Models\Service::find($dataArr->id);
            $service->type = $dataArr->type;
            $service->category_id = $dataArr->category_id;
            $service->sub_category_id = $dataArr->sub_category_id;
            $service->default_image = @$dataArr->images[0];
            $service->name = $dataArr->name;
            $service->en_name = $dataArr->name;
            $service->description = $dataArr->description;
            $service->en_description = $dataArr->description;
            $service->amount = $dataArr->service_amount;
            $service->amount_type = $dataArr->service_amount_type;
            $service->visiting_charges = $dataArr->type == 1 ? $dataArr->visiting_charges : 0;
            $service->address = $dataArr->address;
            $service->latitude = $dataArr->address ? $dataArr->latitude : null;
            $service->longitude = $dataArr->address ? $dataArr->longitude : null;

            // Calculate Daily Amount
            if ($service->amount_type == 1) { // Hourly
                $service->daily_amount = $service->amount * 24;

            } elseif ($service->amount_type == 2) { // Daily
                $service->daily_amount = $service->amount;

            } elseif ($service->amount_type == 3) { // Monthly
                $service->daily_amount = $service->amount / 30;
            }
            $service->save();

            // Link Cities
            if (is_array($dataArr->cities) && count($dataArr->cities)) {
                \App\Models\ServiceCity::where('service_id', $service->id)
                    ->whereNotIn('city_id', $dataArr->cities)
                    ->delete();

                foreach ($dataArr->cities as $city_id) {
                    if (\App\Models\ServiceCity::where('service_id', $service->id)->where('city_id', $city_id)->doesntExist()) {
                        $serviceCity = new \App\Models\ServiceCity();
                        $serviceCity->service_id = $service->id;
                        $serviceCity->city_id = $city_id;
                        $serviceCity->save();
                    }
                }
            }

            // Link Images
            if (is_array($dataArr->images) && count($dataArr->images)) {
                \App\Models\ServiceImage::where('service_id', $service->id)
                    ->whereNotIn('image', $dataArr->images)
                    ->delete();

                $priority = 0;
                $images = \App\Models\ServiceImage::where('service_id', $service->id)->get();
                if ($images->count()) {
                    foreach ($images as $row) {
                        $row->priority = $priority;
                        $row->save();

                        $priority++;
                    }
                }

                foreach ($dataArr->images as $key => $image) {
                    if (\App\Models\ServiceImage::where('service_id', $service->id)->where('image', $image)->doesntExist()) {
                        $serviceImage = new \App\Models\ServiceImage();
                        $serviceImage->service_id = $service->id;
                        $serviceImage->image = $image;
                        $serviceImage->priority = $key;
                        $serviceImage->save();
                    }
                }
            } else {
                \App\Models\ServiceImage::where('service_id', $service->id)->delete();
            }

            // Commit Transaction
            \DB::commit();

            return apiResponse();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function postDelete(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:services,id,vendor_id,{$vendor->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            \App\Models\Service::where('id', $dataArr->id)->delete();

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }
}
