<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class ProductController extends AdminController
{
    protected $img_width = 512;
    protected $img_height = 512;

    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.products.index'), 401);

        return view('admin.products.index');
    }

    public function getList(Request $request)
    {
        $list = \App\Models\Product::select(\DB::raw("products.*, products.{$this->ql}name AS name, vendors.name AS vendor_name, categories.{$this->ql}name AS category, sub_categories.{$this->ql}name AS sub_category"))
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('vendors', 'vendors.id', '=', 'products.vendor_id')
            ->join('categories AS sub_categories', 'sub_categories.id', '=', 'products.sub_category_id');

        return \DataTables::of($list)->make();
    }

    public function getCreate(Request $request)
    {
        abort_unless(hasPermission('admin.products.create'), 401);

        $categories = \App\Models\Category::select(\DB::raw("id, {$this->ql}name AS name"))
            ->whereNull('parent_id')
            ->where('status', 1)
            ->orderBy("{$this->ql}name")
            ->get();

        $vendors = \App\Models\Vendor::select(\DB::raw("id, CONCAT(name, ' (+', dial_code, mobile, ')') AS name"))
            ->where('verification_status', 1)
            ->orderBy("name")
            ->get();

        $cities =  \App\Models\City::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where('status', 1)
            ->orderBy("{$this->ql}name")
            ->get();

        $img_width = $this->img_width;
        $img_height = $this->img_height;

        return view('admin.products.create', compact('img_width', 'img_height', 'categories', 'cities', 'vendors'));
    }

    public function postCreate(Request $request)
    {
        $this->validate($request, [
            'vendor_id' => 'required',
            'category_id' => 'required|numeric|exists:categories,id',
            'sub_category_id' => "required|numeric|exists:categories,id,parent_id,{$request->category_id}",
            'name' => 'required|max:250',
            'en_name' => 'required|max:250',
            'ar_description' => 'required|max:2000',
            'en_description' => 'required|max:2000',
            'is_new' => 'required|in:0,1',
            'new_product_price' => (($request->is_new ? 'required' : 'nullable') . '|gt:0'),
            'total_years' => ((!$request->is_new ? 'required' : 'nullable') . '|gt:0|lt:100'),
            'total_months' => ((!$request->is_new ? 'required' : 'nullable') . '|gte:0|lte:12'),
            'amount' => 'required|numeric|gt:0',
            'amount_type' => 'required|numeric|in:1,2,3', // 1.Hourly, 2.Daily, 3.Monthly
            'delay_charges' => 'required|numeric|gt:0',
            'delay_charges_type' => 'required|numeric|in:1,2,3', // 1.Hourly, 2.Daily, 3.Monthly
            'security_amount' => 'required|numeric|gt:0',
            'city_id' => 'required',
            'is_delivery_available' => 'required|in:0,1',
            'delivery_charges' => ($request->is_delivery_available ? 'required|numeric|gte:0|lte:10000' : 'nullable|numeric|   gte:0|lte:10000'),
            'address' => 'required|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' =>  'required',
        ]);
        $dataArr = arrayFromPost(['vendor_id', 'category_id', 'sub_category_id', 'name', 'ar_description', 'is_new', 'new_product_price', 'total_years', 'total_months', 'amount', 'amount_type', 'delay_charges', 'delay_charges_type', 'security_amount', 'city_id', 'is_delivery_available', 'delivery_charges', 'address', 'latitude', 'longitude', 'image']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $product = new \App\Models\Product();
            $product->vendor_id = $dataArr->vendor_id;
            $product->category_id = $dataArr->category_id;
            $product->sub_category_id = $dataArr->sub_category_id;
            $product->name = $dataArr->name;
            $product->en_name = $dataArr->name;
            $product->description = $dataArr->ar_description;
            $product->en_description = $dataArr->description;
            $product->is_new = $dataArr->is_new;
            $product->amount = $dataArr->amount;
            $product->amount_type = $dataArr->amount_type;
            $product->delay_charges = $dataArr->delay_charges;
            $product->delay_charges_type = $dataArr->delay_charges_type;
            $product->security_amount = $dataArr->security_amount;
            $product->is_delivery_available = $dataArr->is_delivery_available;
            $product->delivery_charges = $dataArr->is_delivery_available ? $dataArr->delivery_charges : 0;
            $product->location = $dataArr->address;
            $product->latitude = $dataArr->address ? $dataArr->latitude : null;
            $product->longitude = $dataArr->address ? $dataArr->longitude : null;
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
            $product->default_image = saveBase64File([
                'width' => $this->img_width,
                'height' => $this->img_height,
                'data_url' => $dataArr->image,
            ]);
            $product->save();

            // Link City
            $productCity = new \App\Models\ProductCity();
            $productCity->product_id = $product->id;
            $productCity->city_id = $dataArr->city_id;
            $productCity->save();

            // Link Image
            $productImage = new \App\Models\ProductImage();
            $productImage->product_id = $product->id;
            $productImage->image = saveBase64File([
                'width' => $this->img_width,
                'height' => $this->img_height,
                'data_url' => $dataArr->image,
            ]);
            $productImage->priority = 1;
            $productImage->save();

            // Commit Transaction
            \DB::commit();

            return successMessage();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function getUpdate(Request $request)
    {
        abort_unless(hasPermission('admin.products.update'), 401);

        $product = \App\Models\Product::findOrFail($request->id);

        $categories = \App\Models\Category::select(\DB::raw("id, {$this->ql}name AS name"))
            ->whereNull('parent_id')
            ->where(function ($query) use ($product) {
                $query->where('status', 1)
                    ->orWhere('id', $product->id);
            })
            ->orderBy("{$this->ql}name")
            ->get();

        $vendors = \App\Models\Vendor::select(\DB::raw("id, CONCAT(name, ' (+', dial_code, mobile, ')') AS name"))
            ->where('verification_status', 1)
            ->orderBy('name')
            ->get();

        $selected_cities = \App\Models\ProductCity::where('product_id', $request->id)
            ->pluck('city_id')
            ->toArray();

        $cities =  \App\Models\City::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where('status', 1)
            ->orWhereIn('id', $selected_cities)
            ->orderBy("{$this->ql}name")
            ->get();

        $img_width = $this->img_width;
        $img_height = $this->img_height;

        return view('admin.products.update', compact('img_width', 'img_height', 'categories', 'cities', 'vendors', 'product', 'selected_cities'));
    }

    public function postUpdate(Request $request)
    {
        $this->validate($request, [
            'vendor_id' => 'required',
            'category_id' => 'required|numeric|exists:categories,id',
            'sub_category_id' => "required|numeric|exists:categories,id,parent_id,{$request->category_id}",
            'name' => 'required|max:250',
            'en_name' => 'required|max:250',
            'ar_description' => 'required|max:2000',
            'en_description' => 'required|max:2000',
            'is_new' => 'required|in:0,1',
            'new_product_price' => (($request->is_new ? 'required' : 'nullable') . '|gt:0'),
            'total_years' => ((!$request->is_new ? 'required' : 'nullable') . '|gt:0|lt:100'),
            'total_months' => ((!$request->is_new ? 'required' : 'nullable') . '|gte:0|lte:12'),
            'amount' => 'required|numeric|gt:0',
            'amount_type' => 'required|numeric|in:1,2,3', // 1.Hourly, 2.Daily, 3.Monthly
            'delay_charges' => 'required|numeric|gt:0',
            'delay_charges_type' => 'required|numeric|in:1,2,3', // 1.Hourly, 2.Daily, 3.Monthly
            'security_amount' => 'required|numeric|gt:0',
            'city_id' => 'required',
            'is_delivery_available' => 'required|in:0,1',
            'delivery_charges' => ($request->is_delivery_available ? 'required|numeric|gte:0|lte:10000' : 'nullable|numeric|   gte:0|lte:10000'),
            'address' => 'required|max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        $dataArr = arrayFromPost(['vendor_id', 'category_id', 'sub_category_id', 'name', 'description', 'is_new', 'new_product_price', 'total_years', 'total_months', 'amount', 'amount_type', 'delay_charges', 'delay_charges_type', 'security_amount', 'city_id', 'is_delivery_available', 'delivery_charges', 'address', 'latitude', 'longitude']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $product = \App\Models\Product::find($request->id);
            $product->vendor_id = $dataArr->vendor_id;
            $product->category_id = $dataArr->category_id;
            $product->sub_category_id = $dataArr->sub_category_id;
            $product->name = $dataArr->name;
            $product->en_name = $dataArr->name;
            $product->description = $dataArr->description;
            $product->en_description = $dataArr->description;
            $product->is_new = $dataArr->is_new;
            $product->amount = $dataArr->amount;
            $product->amount_type = $dataArr->amount_type;
            $product->delay_charges = $dataArr->delay_charges;
            $product->delay_charges_type = $dataArr->delay_charges_type;
            $product->security_amount = $dataArr->security_amount;
            $product->is_delivery_available = $dataArr->is_delivery_available;
            $product->delivery_charges = $dataArr->is_delivery_available ? $dataArr->delivery_charges : 0;
            $product->location = $dataArr->address;
            $product->latitude = $dataArr->address ? $dataArr->latitude : null;
            $product->longitude = $dataArr->address ? $dataArr->longitude : null;
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

            // Link Cities
            \App\Models\ProductCity::where('product_id', $product->id)
                ->where('city_id', $dataArr->city_id)
                ->delete();

            if (\App\Models\ProductCity::where('product_id', $product->id)->where('city_id', $dataArr->city_id)->doesntExist()) {
                $productCity = new \App\Models\ProductCity();
                $productCity->product_id = $product->id;
                $productCity->city_id = $dataArr->city_id;
                $productCity->save();
            }

            // Commit Transaction
            \DB::commit();

            return successMessage();
        } catch (\Throwable $th) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($th);
        }
    }

    public function getDelete(Request $request)
    {
        abort_unless(hasPermission('admin.products.delete'), 401);

        \App\Models\Product::where('id', $request->id)->delete();
        return successMessage();
    }

    public function getView(Request $request)
    {
        abort_unless(hasPermission('admin.products.view'), 401);

        $product =  \App\Models\Product::findOrFail($request->id);
        $product->category = \App\Models\Category::where('id', $product->category_id)->value("{$this->ql}name");
        $product->sub_category = \App\Models\Category::where('id', $product->sub_category_id)->value("{$this->ql}name");
        $product->vendor_name = \App\Models\Vendor::where('id', $product->vendor_id)->value('name');

        $product_image = \App\Models\ProductImage::select(\DB::Raw("id, image, priority"))
            ->where('product_id', $product->id)
            ->orderBy('priority')
            ->get();

        $img_width = $this->img_width;
        $img_height = $this->img_height;
        return view('admin.products.view', compact('product', 'product_image', 'img_width', 'img_height'));
    }

    // Image Fns
    public function getImageCreate(Request $request)
    {
        abort_unless(hasPermission('admin.products.images.create'), 401);

        $img_width = $this->img_width;
        $img_height = $this->img_height;
        return view('admin.products.images.create', compact('img_width', 'img_height'));
    }

    public function postImageCreate(Request $request)
    {
        $this->validate($request, [
            'image' => 'required'
        ]);

        $dataArr = arrayFromPost(['image']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $productImg = new \App\Models\ProductImage();
            $productImg->product_id = $request->id;
            $productImg->priority = \App\Models\ProductImage::where('product_id', $request->id)->max('priority') + 1;
            $productImg->image = saveBase64File([
                'width' => $this->img_width,
                'height' => $this->img_height,
                'data_url' => $dataArr->image,
            ]);
            $productImg->save();

            // Commit Transaction
            \DB::commit();

            return successMessage();
        } catch (\Throwable $th) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($th);
        }
    }

    public function getImageDelete(Request $request)
    {
        abort_unless(hasPermission('admin.products.images.delete'), 401);

        try {
            if (\App\Models\ProductImage::where('product_id', $request->product_id)->count() == 1) {
                return errorMessage('cant_delete_last_image');
            }

            \App\Models\ProductImage::where('id', $request->id)->delete();
            return successMessage();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postChangeImageOrder(Request $request)
    {
        $this->validate($request, [
            'order' => 'required|array'
        ]);
        $dataArr = arrayFromPost(['order']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            if (is_array($dataArr->order) && count($dataArr->order)) {
                foreach ($dataArr->order as $key => $product_image_id) {
                    $product_image = \App\Models\ProductImage::find($product_image_id);
                    $product_image->priority = $key + 1;
                    $product_image->save();
                }
            }

            // Commit Transaction
            \DB::commit();

            return successMessage();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    // Reviews Fns
    public function getReviewList(Request $request)
    {
        $list = \App\Models\ProductReview::select(\DB::raw("product_reviews.*, users.name AS user, products.{$this->ql}name as product, product_bookings.booking_code"))
            ->leftJoin('users', 'users.id', '=', 'product_reviews.user_id')
            ->leftJoin('products', 'products.id', '=', 'product_reviews.product_id')
            ->leftJoin('product_bookings', 'product_bookings.id', '=', 'product_reviews.product_booking_id')
            ->when(!blank($request->id), function ($query) use ($request) {
                $query->where('product_reviews.product_id', $request->id);
            })
            ->when(!blank($request->user_id), function ($query) use ($request) {
                $query->where('product_reviews.user_id', $request->user_id);
            });

        return \DataTables::of($list)
            ->editColumn('rating', function ($query) {
                return number_format($query->rating);
            })->make();
    }

    public function getReviewDelete(Request $request)
    {
        abort_unless(hasPermission('admin.products.reviews.delete'), 401);
        \App\Models\ProductReview::where('id', $request->id)->delete();
        return successMessage();
    }
}
