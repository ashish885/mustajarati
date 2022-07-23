<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class ProductController extends VendorController
{
    public function getList(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'page' => 'required|numeric|min:1',
        ]);

        try {
            $list = \App\Models\Product::select(\DB::raw("products.id, products.default_image, products.{$this->ql}name AS name, products.total_bookings, products.total_ratings, products.avg_rating, products.daily_amount, categories.{$this->ql}name AS category, sub_categories.{$this->ql}name AS sub_category, products.amount, products.amount_type, products.is_sponsored"))
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->join('categories AS sub_categories', 'sub_categories.id', '=', 'products.sub_category_id')
                ->where('products.vendor_id', $vendor->id)
                ->orderBy('products.id', 'DESC')
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
            'id' => "required|numeric|exists:products,id,vendor_id,{$vendor->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $product = \App\Models\Product::find($dataArr->id);
            if (!blank($product)) {
                $product->category = \App\Models\Category::where('id', $product->category_id)->value("{$this->ql}name");
                $product->sub_category = \App\Models\Category::where('id', $product->sub_category_id)->value("{$this->ql}name");
                $product->features = (array) @unserialize($product->features);
                $product->en_features = (array) @unserialize($product->en_features);

                $product->images = \App\Models\ProductImage::where('product_id', $product->id)
                    ->orderBy('priority')
                    ->pluck('image');

                $product->cities = \App\Models\City::select(\DB::raw("cities.id, cities.{$this->ql}name as name"))
                    ->join('product_cities', 'product_cities.city_id', '=', 'cities.id')
                    ->where('product_cities.product_id', $product->id)
                    ->get();
            }

            return apiResponse('success', $product);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postAdd(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'category_id' => 'required|numeric|exists:categories,id',
            'sub_category_id' => "required|numeric|exists:categories,id,parent_id,{$request->category_id}",
            'name' => 'required|max:250',
            'description' => 'required|max:2000',
            'features' => 'required|array',
            'features.*' => 'required|max:1000',
            'is_new' => 'required|in:0,1',
            'new_product_price' => (($request->is_new ? 'required' : 'nullable') . '|gt:0'),
            'total_years' => ((!$request->is_new ? 'required' : 'nullable') . '|gt:0|lt:100'),
            'total_months' => ((!$request->is_new ? 'required' : 'nullable') . '|gte:0|lte:12'),
            'rent_amount' => 'required|numeric|gt:0',
            'rent_amount_type' => 'required|numeric|in:1,2,3', // 1.Hourly, 2.Daily, 3.Monthly
            'delay_charges' => 'nullable|numeric|gte:0',
            'delay_charges_type' => 'nullable|numeric|in:1,2,3', // 1.Hourly, 2.Daily, 3.Monthly
            'security_amount' => 'nullable|numeric|gte:0',
            'cities' => 'required|array',
            'cities.*' => 'required|distinct|numeric|exists:cities,id',
            'is_delivery_available' => 'required|in:0,1',
            'delivery_charges' => ($request->is_delivery_available ? 'required|numeric|gte:0|lte:10000' : 'nullable|numeric|gte:0|lte:10000'),
            'location' => 'required|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images' => 'required|array|max:5',
            'images.*' => 'required',
        ]);
        $dataArr = arrayFromPost(['category_id', 'sub_category_id', 'name', 'description', 'features', 'is_new', 'new_product_price', 'total_years', 'total_months', 'rent_amount', 'rent_amount_type', 'delay_charges', 'delay_charges_type', 'security_amount', 'cities', 'is_delivery_available', 'delivery_charges', 'location', 'latitude', 'longitude', 'images']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $product = new \App\Models\Product();
            $product->vendor_id = $vendor->id;
            $product->category_id = $dataArr->category_id;
            $product->sub_category_id = $dataArr->sub_category_id;
            $product->default_image = @$dataArr->images[0];
            $product->name = $dataArr->name;
            $product->en_name = $dataArr->name;
            $product->description = $dataArr->description;
            $product->en_description = $dataArr->description;
            $product->features = serialize($dataArr->features);
            $product->en_features = serialize($dataArr->features);
            $product->is_new = $dataArr->is_new;
            $product->amount = $dataArr->rent_amount;
            $product->amount_type = $dataArr->rent_amount_type;
            if ($dataArr->delay_charges && $dataArr->delay_charges_type) {
                $product->delay_charges = $dataArr->delay_charges;
                $product->delay_charges_type = $dataArr->delay_charges_type;
            }
            $product->security_amount = $dataArr->security_amount ? $dataArr->security_amount : 0;
            $product->is_delivery_available = $dataArr->is_delivery_available;
            $product->delivery_charges = $dataArr->is_delivery_available ? $dataArr->delivery_charges : 0;
            $product->location = $dataArr->location;
            $product->latitude = $dataArr->location ? $dataArr->latitude : null;
            $product->longitude = $dataArr->location ? $dataArr->longitude : null;
            if ($product->is_new) {
                $product->new_product_price = $dataArr->new_product_price;
            } else {
                $manufacturing_date = date('Y-m-d', strtotime("-{$dataArr->total_years} YEARS"));
                $manufacturing_date = date('Y-m-d', strtotime("-{$dataArr->total_months} MONTHS {$manufacturing_date}"));

                $product->manufacturing_date = $manufacturing_date;
            }

            // Calculate Daily Amount
            if ($product->amount_type == 1) { // Hourly
                $product->daily_amount = $product->amount * 24;

            } elseif ($product->amount_type == 2) { // Daily
                $product->daily_amount = $product->amount;

            } elseif ($product->amount_type == 3) { // Monthly
                $product->daily_amount = $product->amount / 30;
            }
            $product->save();

            // Link Cities
            if (is_array($dataArr->cities) && count($dataArr->cities)) {
                foreach ($dataArr->cities as $city_id) {
                    $productCity = new \App\Models\ProductCity();
                    $productCity->product_id = $product->id;
                    $productCity->city_id = $city_id;
                    $productCity->save();
                }
            }

            // Link Images
            if (is_array($dataArr->images) && count($dataArr->images)) {
                foreach ($dataArr->images as $key => $image) {
                    $productImage = new \App\Models\ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = $image;
                    $productImage->priority = $key;
                    $productImage->save();
                }
            }

            // Commit Transaction
            \DB::commit();

            return apiResponse('success', ['id' => $product->id]);
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
            'id' => "required|numeric|exists:products,id,vendor_id,{$vendor->id}",
            'category_id' => 'required|numeric|exists:categories,id',
            'sub_category_id' => "required|numeric|exists:categories,id,parent_id,{$request->category_id}",
            'name' => 'required|max:250',
            'description' => 'required|max:2000',
            'features' => 'required|array',
            'features.*' => 'required|max:1000',
            'is_new' => 'required|in:0,1',
            'new_product_price' => (($request->is_new ? 'required' : 'nullable') . '|gt:0'),
            'total_years' => ((!$request->is_new ? 'required' : 'nullable') . '|gt:0|lt:100'),
            'total_months' => ((!$request->is_new ? 'required' : 'nullable') . '|gte:0|lte:12'),
            'rent_amount' => 'required|numeric|gt:0',
            'rent_amount_type' => 'required|numeric|in:1,2,3', // 1.Hourly, 2.Daily, 3.Monthly
            'delay_charges' => 'nullable|numeric|gte:0',
            'delay_charges_type' => 'nullable|numeric|in:1,2,3', // 1.Hourly, 2.Daily, 3.Monthly
            'security_amount' => 'nullable|numeric|gte:0',
            'cities' => 'required|array',
            'cities.*' => 'required|distinct|numeric|exists:cities,id',
            'is_delivery_available' => 'required|in:0,1',
            'delivery_charges' => ($request->is_delivery_available ? 'required|numeric|gte:0|lte:10000' : 'nullable|numeric|gte:0|lte:10000'),
            'location' => 'required|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images' => 'required|array|max:5',
            'images.*' => 'required',
        ]);
        $dataArr = arrayFromPost(['id', 'category_id', 'sub_category_id', 'name', 'description', 'features', 'is_new', 'new_product_price', 'total_years', 'total_months', 'rent_amount', 'rent_amount_type', 'delay_charges', 'delay_charges_type', 'security_amount', 'cities', 'is_delivery_available', 'delivery_charges', 'location', 'latitude', 'longitude', 'images']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $product = \App\Models\Product::find($dataArr->id);
            $product->category_id = $dataArr->category_id;
            $product->sub_category_id = $dataArr->sub_category_id;
            $product->default_image = @$dataArr->images[0];
            $product->name = $dataArr->name;
            $product->en_name = $dataArr->name;
            $product->description = $dataArr->description;
            $product->en_description = $dataArr->description;
            $product->features = serialize($dataArr->features);
            $product->en_features = serialize($dataArr->features);
            $product->is_new = $dataArr->is_new;
            $product->amount = $dataArr->rent_amount;
            $product->amount_type = $dataArr->rent_amount_type;
            if ($dataArr->delay_charges && $dataArr->delay_charges_type) {
                $product->delay_charges = $dataArr->delay_charges;
                $product->delay_charges_type = $dataArr->delay_charges_type;
            }
            $product->security_amount = $dataArr->security_amount ? $dataArr->security_amount : 0;
            $product->is_delivery_available = $dataArr->is_delivery_available;
            $product->delivery_charges = $dataArr->is_delivery_available ? $dataArr->delivery_charges : 0;
            $product->location = $dataArr->location;
            $product->latitude = $dataArr->location ? $dataArr->latitude : null;
            $product->longitude = $dataArr->location ? $dataArr->longitude : null;
            if ($product->is_new) {
                $product->new_product_price = $dataArr->new_product_price;
                $product->manufacturing_date = null;
            } else {
                $manufacturing_date = date('Y-m-d', strtotime("-{$dataArr->total_years} YEARS"));
                $manufacturing_date = date('Y-m-d', strtotime("-{$dataArr->total_months} MONTHS {$manufacturing_date}"));

                $product->manufacturing_date = $manufacturing_date;
                $product->new_product_price = null;
            }

            // Calculate Daily Amount
            if ($product->amount_type == 1) { // Hourly
                $product->daily_amount = $product->amount * 24;

            } elseif ($product->amount_type == 2) { // Daily
                $product->daily_amount = $product->amount;

            } elseif ($product->amount_type == 3) { // Monthly
                $product->daily_amount = $product->amount / 30;
            }
            $product->save();

            // Link Cities
            if (is_array($dataArr->cities) && count($dataArr->cities)) {
                \App\Models\ProductCity::where('product_id', $product->id)
                    ->whereNotIn('city_id', $dataArr->cities)
                    ->delete();

                foreach ($dataArr->cities as $city_id) {
                    if (\App\Models\ProductCity::where('product_id', $product->id)->where('city_id', $city_id)->doesntExist()) {
                        $productCity = new \App\Models\ProductCity();
                        $productCity->product_id = $product->id;
                        $productCity->city_id = $city_id;
                        $productCity->save();
                    }
                }
            }

            // Link Images
            if (is_array($dataArr->images) && count($dataArr->images)) {
                \App\Models\ProductImage::where('product_id', $product->id)
                    ->whereNotIn('image', $dataArr->images)
                    ->delete();

                $priority = 0;
                $images = \App\Models\ProductImage::where('product_id', $product->id)->get();
                if ($images->count()) {
                    foreach ($images as $row) {
                        $row->priority = $priority;
                        $row->save();

                        $priority++;
                    }
                }

                foreach ($dataArr->images as $image) {
                    if (\App\Models\ProductImage::where('product_id', $product->id)->where('image', $image)->doesntExist()) {
                        $productImage = new \App\Models\ProductImage();
                        $productImage->product_id = $product->id;
                        $productImage->image = $image;
                        $productImage->priority = $priority;
                        $productImage->save();

                        $priority++;
                    }
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

    public function postDelete(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:products,id,vendor_id,{$vendor->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            \App\Models\Product::where('id', $dataArr->id)->delete();

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }
}
