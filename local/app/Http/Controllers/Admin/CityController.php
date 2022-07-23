<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class CityController extends AdminController
{
    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.cities.index'), 401);

        return view('admin.cities.index');
    }

    public function getList(Request $request)
    {
        $list = \App\Models\City::select(\DB::raw("cities.*, cities.{$this->ql}name AS name, countries.{$this->ql}name AS country_name"))
            ->join('countries', 'countries.id', '=', 'cities.country_id');

        return \DataTables::of($list)
            ->addColumn('status_text', function ($query) {
                return transLang('action_status')[$query->status];
            })
            ->make();
    }

    public function getCreate(Request $request)
    {
        abort_unless(hasPermission('admin.cities.create'), 401);

        $countries = \App\Models\Country::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where('status', 1)
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.cities.create', compact('countries'));
    }

    public function postCreate(Request $request)
    {
        $this->validate($request, [
            'country_id' => 'required',
            'name' => 'required|max:250',
            'en_name' => 'required|max:250',
            'status' => 'required',
        ]);
        $dataArr = arrayFromPost(['country_id', 'name', 'en_name', 'status']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $city = new \App\Models\City();
            $city->country_id = $dataArr->country_id;
            $city->name = $dataArr->name;
            $city->en_name = $dataArr->en_name;
            $city->status = $dataArr->status;
            $city->save();

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
        abort_unless(hasPermission('admin.cities.update'), 401);

        $city = \App\Models\City::findOrFail($request->id);

        $countries = \App\Models\Country::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where('status', 1)
            ->orWhere('id', $city->country_id)
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.cities.update', compact('city', 'countries'));
    }

    public function postUpdate(Request $request)
    {
        $this->validate($request, [
            'country_id' => 'required',
            'name' => 'required|max:250',
            'en_name' => 'required|max:250',
            'status' => 'required',
        ]);
        $dataArr = arrayFromPost(['country_id', 'name', 'en_name', 'status']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $city = \App\Models\City::find($request->id);
            if (!blank($city)) {
                $city->country_id = $dataArr->country_id;
                $city->name = $dataArr->name;
                $city->en_name = $dataArr->en_name;
                $city->status = $dataArr->status;
                $city->save();
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
        abort_unless(hasPermission('admin.cities.delete'), 401);

        $city = \App\Models\City::where('id', $request->id)->delete();
        return successMessage();
    }
}
