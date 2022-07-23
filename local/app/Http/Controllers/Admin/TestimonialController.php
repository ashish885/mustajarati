<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class TestimonialController extends AdminController
{
    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.testimonial.index'), 401);

        return view('admin.testimonial.index');
    }

    public function getList(Request $request)
    {
        $list = \App\Models\Testimonial::select(\DB::raw("testimonials.*, testimonials.{$this->ql}title as title, cities.{$this->ql}name as city_name"))
            ->leftJoin('cities', 'cities.id', '=', 'testimonials.city_id');
        return \DataTables::of($list)->make();
    }

    public function getCreate(Request $request)
    {
        abort_unless(hasPermission('admin.testimonial.create'), 401);

        $cities = \App\Models\City::select(\DB::raw("id, {$this->ql}name AS name"))
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.testimonial.create', compact('cities'));
    }

    public function postCreate(Request $request)
    {
        $this->validate($request, [
            'ar_title' => 'required|max:300',
            'en_title' => 'required|max:300',
            'ar_description' => 'required|max:1000',
            'en_description' => 'required|max:1000',
            'customer_name' => 'required',
            'city' => 'required',
            'rating' => 'required|numeric|in:1,2,3,4,5',
        ]);
        $dataArr = arrayFromPost(['customer_name', 'city', 'ar_title', 'en_title', 'ar_description', 'en_description', 'rating']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $testimonial = new \App\Models\Testimonial();
            $testimonial->customer_name = $dataArr->customer_name;
            $testimonial->city_id = $dataArr->city;
            $testimonial->title = $dataArr->ar_title;
            $testimonial->en_title = $dataArr->en_title;
            $testimonial->description = $dataArr->ar_description;
            $testimonial->en_description = $dataArr->en_description;
            $testimonial->rating = $dataArr->rating;
            $testimonial->save();

            \Cache::forget('testimonials');

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
        abort_unless(hasPermission('admin.testimonial.update'), 401);

        $testimonial = \App\Models\Testimonial::findOrFail($request->id);

        $cities = \App\Models\City::select(\DB::raw("id, {$this->ql}name AS name"))
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.testimonial.update', compact('cities', 'testimonial'));
    }

    public function postUpdate(Request $request)
    {
        $this->validate($request, [
            'ar_title' => 'required|max:300',
            'en_title' => 'required|max:300',
            'ar_description' => 'required|max:1000',
            'en_description' => 'required|max:1000',
            'customer_name' => 'required',
            'city' => 'required',
            'rating' => 'required|numeric|in:1,2,3,4,5',
        ]);
        $dataArr = arrayFromPost(['customer_name', 'city', 'ar_title', 'en_title', 'ar_description', 'en_description', 'rating']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $testimonial = \App\Models\Testimonial::find($request->id);
            $testimonial->customer_name = $dataArr->customer_name;
            $testimonial->city_id = $dataArr->city;
            $testimonial->title = $dataArr->ar_title;
            $testimonial->en_title = $dataArr->en_title;
            $testimonial->description = $dataArr->ar_description;
            $testimonial->en_description = $dataArr->en_description;
            $testimonial->rating = $dataArr->rating;
            $testimonial->save();

            \Cache::forget('testimonials');

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
        abort_unless(hasPermission('admin.testimonial.delete'), 401);

        \App\Models\Testimonial::where('id', $request->id)->delete();

        \Cache::forget('testimonials');
        return successMessage();
    }
}
