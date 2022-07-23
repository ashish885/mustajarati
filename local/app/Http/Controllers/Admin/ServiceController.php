<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class ServiceController extends AdminController
{
    protected $img_width = 512;
    protected $img_height = 512;

    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.services.index'), 401);

        return view('admin.services.index');
    }

    public function getList(Request $request)
    {
        $list = \App\Models\Service::select(\DB::raw("services.*, services.{$this->ql}name AS name, vendors.name AS vendor_name, categories.{$this->ql}name AS category, sub_categories.{$this->ql}name AS sub_category"))
            ->join('categories', 'categories.id', '=', 'services.category_id')
            ->join('vendors', 'vendors.id', '=', 'services.vendor_id')
            ->join('categories AS sub_categories', 'sub_categories.id', '=', 'services.sub_category_id');

        return \DataTables::of($list)
            ->addColumn('amount_type', function ($query) {
                return transLang('amount_type_arr')[$query->amount_type];
            })
            ->make();
    }

    public function getCreate(Request $request)
    {
        abort_unless(hasPermission('admin.services.create'), 401);

        $vendors = \App\Models\Vendor::select(\DB::raw('id, CONCAT(name, " (", email, ")") AS name'))
            ->where('is_profile_verified', 1)
            ->orderBy("name")
            ->get();

        $categories = \App\Models\Category::select(\DB::raw("id, {$this->ql}name AS name"))
            ->whereNull('parent_id')
            ->orderBy("{$this->ql}name")
            ->get();

        $img_width = $this->img_width;
        $img_height = $this->img_height;
        return view('admin.services.create', compact('vendors', 'categories', 'img_width', 'img_height'));
    }

    public function postCreate(Request $request)
    {
        $this->validate($request, [
            'vendor_id' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'ar_name' => 'required|max:250',
            'en_name' => 'required|max:250',
            'ar_description' => 'required|max:1000',
            'en_description' => 'required|max:1000',
            'amount' => 'required|numeric|gte:0|lte:5000',
            'amount_type' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'action_type' => 'required',
            'status' => 'required',
            'image' => 'required',
        ]);
        $dataArr = arrayFromPost(['vendor_id', 'category_id', 'sub_category_id', 'ar_name', 'en_name', 'ar_description', 'en_description', 'amount', 'address', 'latitude', 'longitude', 'amount', 'action_type', 'amount_type', 'status', 'image']);

        try {
            // Start Transaction
            \DB::beginTransaction();
            $service = new \App\Models\Service();
            $service->vendor_id = $dataArr->vendor_id;
            $service->category_id = $dataArr->category_id;
            $service->type = $dataArr->action_type;
            $service->sub_category_id = $dataArr->sub_category_id;
            $service->name = $dataArr->ar_name;
            $service->en_name = $dataArr->en_name;
            $service->description = $dataArr->ar_description;
            $service->en_description = $dataArr->en_description;
            $service->amount = $dataArr->amount;
            $service->address = $dataArr->address;
            $service->latitude = $dataArr->latitude;
            $service->longitude = $dataArr->longitude;
            $service->amount = $dataArr->amount;
            $service->amount_type = $dataArr->amount_type;
            $service->status = $dataArr->status;
            $service->default_image = saveBase64File([
                'width' => $this->img_width,
                'height' => $this->img_height,
                'data_url' => $dataArr->image,
            ]);
            $service->save();

            $serviceImg = new \App\Models\ServiceImage();
            $serviceImg->service_id = $service->id;
            $serviceImg->image = $service->default_image;
            $serviceImg->priority = 1;
            $serviceImg->save();

            // Commit Transaction
            \DB::commit();
            return successMessage();
        } catch (\Throwable $th) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($th);
        }
    }

    public function getUpdate(Request $request)
    {
        abort_unless(hasPermission('admin.services.update'), 401);

        $service = \App\Models\Service::findOrFail($request->id);

        $vendors = \App\Models\Vendor::select(\DB::raw('id, CONCAT(name, " (", email, ")") AS name'))
            ->where('is_profile_verified', 1)
            ->orderBy("name")
            ->get();

        $categories = \App\Models\Category::select(\DB::raw("id, {$this->ql}name AS name"))
            ->whereNull('parent_id')
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.services.update', compact('vendors', 'categories', 'service'));
    }

    public function postUpdate(Request $request)
    {

        $this->validate($request, [
            'vendor_id' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'ar_name' => 'required|max:250',
            'en_name' => 'required|max:250',
            'ar_description' => 'required|max:1000',
            'en_description' => 'required|max:1000',
            'amount' => 'required|numeric|gte:0|lte:5000',
            'amount_type' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'action_type' => 'required',
            'status' => 'required',
        ]);
        $dataArr = arrayFromPost(['vendor_id', 'category_id', 'sub_category_id', 'ar_name', 'en_name', 'ar_description', 'en_description', 'amount', 'address', 'latitude', 'longitude', 'amount', 'action_type', 'amount_type', 'status']);
        try {
            // Start Transaction
            \DB::beginTransaction();

            $service = \App\Models\Service::find($request->id);
            $service->vendor_id = $dataArr->vendor_id;
            $service->category_id = $dataArr->category_id;
            $service->type = $dataArr->action_type;
            $service->sub_category_id = $dataArr->sub_category_id;
            $service->name = $dataArr->ar_name;
            $service->en_name = $dataArr->en_name;
            $service->description = $dataArr->ar_description;
            $service->en_description = $dataArr->en_description;
            $service->amount = $dataArr->amount;
            $service->address = $dataArr->address;
            $service->latitude = $dataArr->latitude;
            $service->longitude = $dataArr->longitude;
            $service->amount = $dataArr->amount;
            $service->amount_type = $dataArr->amount_type;
            $service->status = $dataArr->status;
            $service->save();
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
        abort_unless(hasPermission('admin.services.delete'), 401);

        \App\Models\Service::where('id', $request->id)->delete();
        return successMessage();
    }

    public function getView(Request $request)
    {
        abort_unless(hasPermission('admin.services.view'), 401);

        $service = \App\Models\Service::findOrFail($request->id);
        $service->vendor = \App\Models\Vendor::where('id', $service->vendor_id)->value('name');
        $service->category = \App\Models\Category::where('id', $service->category_id)->value("{$this->ql}name");
        $service->sub_category = \App\Models\Category::where('id', $service->sub_category_id)->value("{$this->ql}name");

        $service_image = \App\Models\ServiceImage::select(\DB::Raw("id, image, priority"))
            ->where('service_id', $request->id)
            ->orderBy('priority')
            ->get();
        $img_width = $this->img_width;
        $img_height = $this->img_height;
        return view('admin.services.view', compact('service', 'service_image', 'img_height', 'img_width'));
    }

    // Image Fns
    public function getImageCreate(Request $request)
    {
        abort_unless(hasPermission('admin.services.images.create'), 401);

        $priority = \App\Models\ServiceImage::where('service_id', $request->id)->max('priority') + 1;
        $id = $request->id;
        $img_width = $this->img_width;
        $img_height = $this->img_height;
        return view('admin.services.images.create', compact('img_width', 'id', 'img_height', 'priority'));
    }

    public function postImageCreate(Request $request)
    {
        $this->validate($request, [
            'image' => 'required',
        ]);
        $dataArr = arrayFromPost(['image']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $serviceImg = new \App\Models\ServiceImage();
            $serviceImg->service_id = $request->id;
            $serviceImg->priority = \App\Models\ServiceImage::where('service_id', $request->id)->max('priority') + 1;
            $serviceImg->image = saveBase64File([
                'width' => $this->img_width,
                'height' => $this->img_height,
                'data_url' => $dataArr->image,
            ]);
            $serviceImg->save();

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
        abort_unless(hasPermission('admin.services.images.delete'), 401);

        try {
            if (\App\Models\ServiceImage::where('service_id', $request->service_id)->count() == 1) {
                return errorMessage('cant_delete_last_image');
            }

            \App\Models\ServiceImage::where('id', $request->id)->delete();
            return successMessage();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    // Reviews Fns
    public function getReviewList(Request $request)
    {
        $list = \App\Models\ServiceReview::select(\DB::raw("service_reviews.*, users.name AS user, service_bookings.booking_code, services.{$this->ql}name AS service"))
            ->leftJoin('users', 'users.id', '=', 'service_reviews.user_id')
            ->leftJoin('services', 'services.id', '=', 'service_reviews.service_id')
            ->leftJoin('service_bookings', 'service_bookings.id', '=', 'service_reviews.service_booking_id')
            ->when(!blank($request->id), function ($query) use ($request) {
                $query->where('service_reviews.service_id', $request->id);
            })
            ->when(!blank($request->user_id), function ($query) use ($request) {
                $query->where('service_reviews.user_id', $request->user_id);
            });

        return \DataTables::of($list)
            ->addColumn('rating', function ($query) {
                return number_format($query->rating);
            })->make();
    }

    public function getReviewDelete(Request $request)
    {

        abort_unless(hasPermission('admin.services.reviews.delete'), 401);

        \App\Models\ServiceReview::where('id', $request->id)->delete();
        return successMessage();
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
                foreach ($dataArr->order as $key => $service_image_id) {
                    $product_image = \App\Models\ServiceImage::find($service_image_id);
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
}
