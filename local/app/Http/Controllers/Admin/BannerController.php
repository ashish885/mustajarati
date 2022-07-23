<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class BannerController extends AdminController
{
    private $imgDimension = [680, 253]; // WXH

    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.banners.index'), 401);

        return view('admin.banners.index');
    }

    public function getList(Request $request)
    {
        $banners = \App\Models\Banner::select(\DB::raw("banners.*, products.{$this->ql}name AS product, services.{$this->ql}name AS service"))
            ->leftJoin('products', 'products.id', '=', 'banners.product_id')
            ->leftJoin('services', 'services.id', '=', 'banners.service_id');

        return \DataTables::of($banners)
            ->addColumn('status_text', function ($query) {
                return transLang('action_status')[$query->status];
            })
            ->addColumn('click_type_text', function ($query) {
                return transLang('click_type_arr')[$query->click_type];
            })
            ->make();
    }

    public function getCreate(Request $request)
    {
        abort_unless(hasPermission('admin.banners.create'), 401);

        $imgDimension = $this->imgDimension;

        $products = \App\Models\Product::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where('status', 1)
            ->orderBy("{$this->ql}name")
            ->get();

        $services = \App\Models\Service::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where('status', 1)
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.banners.create', compact('products', 'services', 'imgDimension'));
    }

    public function postCreate(Request $request)
    {
        $this->validate($request, [
            'image' => "required|image|dimensions:width={$this->imgDimension[0]},height={$this->imgDimension[1]}",
            'en_image' => "required|image|dimensions:width={$this->imgDimension[0]},height={$this->imgDimension[1]}",
            'click_type' => 'required|in:1,2,3', // 1.None, 2.Product, 3.Service
            'product_id' => ($request->click_type == 2 ? 'required' : 'nullable'),
            'service_id' => ($request->click_type == 3 ? 'required' : 'nullable'),
            'status' => 'required',
        ]);
        $dataArr = arrayFromPost(['click_type', 'product_id', 'service_id', 'status']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $banner = new \App\Models\Banner();
            $banner->image = uploadFile('image');
            $banner->en_image = uploadFile('en_image');
            $banner->click_type = $dataArr->click_type;
            $banner->product_id = $dataArr->product_id;
            $banner->service_id = $dataArr->service_id;
            $banner->status = $dataArr->status;
            $banner->save();

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
        abort_unless(hasPermission('admin.banners.update'), 401);

        $banner = \App\Models\Banner::findOrFail($request->id);
        $imgDimension = $this->imgDimension;

        $products = \App\Models\Product::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where('status', 1)
            ->orderBy("{$this->ql}name")
            ->get();

        $services = \App\Models\Service::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where('status', 1)
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.banners.update', compact('banner', 'products', 'services', 'imgDimension'));
    }

    public function postUpdate(Request $request)
    {
        $this->validate($request, [
            'image' => "nullable|image|dimensions:width={$this->imgDimension[0]},height={$this->imgDimension[1]}",
            'en_image' => "nullable|image|dimensions:width={$this->imgDimension[0]},height={$this->imgDimension[1]}",
            'click_type' => 'required|in:1,2,3', // 1.None, 2.Product, 3.Service
            'product_id' => ($request->click_type == 2 ? 'required' : 'nullable'),
            'service_id' => ($request->click_type == 3 ? 'required' : 'nullable'),
            'status' => 'required',
        ]);
        $dataArr = arrayFromPost(['click_type', 'product_id', 'service_id', 'status']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $banner = \App\Models\Banner::find($request->id);
            if ($request->image) {
                $banner->image = uploadFile('image');
            }
            if ($request->image) {
                $banner->en_image = uploadFile('en_image');
            }
            $banner->click_type = $dataArr->click_type;
            $banner->product_id = $dataArr->product_id;
            $banner->service_id = $dataArr->service_id;
            $banner->status = $dataArr->status;
            $banner->save();

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
        abort_unless(hasPermission('admin.banners.delete'), 401);

        $banner = \App\Models\Banner::where('id', $request->id)->delete();
        return successMessage();
    }
}
