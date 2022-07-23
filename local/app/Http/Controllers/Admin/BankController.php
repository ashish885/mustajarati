<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class BankController extends AdminController
{
    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.banks.index'), 401);

        return view('admin.banks.index');
    }

    public function getList(Request $request)
    {
        $list = \App\Models\Bank::select(\DB::raw("banks.*, banks.{$this->ql}name AS name"));
        return \DataTables::of($list)
            ->addColumn('status_text', function ($query) {
                return transLang('action_status')[$query->status];
            })
            ->make();
    }

    public function getCreate(Request $request)
    {
        abort_unless(hasPermission('admin.banks.create'), 401);
        return view('admin.banks.create');
    }

    public function postCreate(Request $request)
    {
        $this->validate($request, [
            'ar_name' => 'required|max:250|unique:banks,name',
            'en_name' => 'required|max:250|unique:banks',
            'status' => 'required',
        ]);
        $dataArr = arrayFromPost(['ar_name', 'en_name', 'status']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $bank = new \App\Models\Bank();
            $bank->name = $dataArr->ar_name;
            $bank->en_name = $dataArr->en_name;
            $bank->status = $dataArr->status;
            $bank->save();

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
        abort_unless(hasPermission('admin.banks.update'), 401);

        $bank = \App\Models\Bank::findOrFail($request->id);
        return view('admin.banks.update', compact('bank'));
    }

    public function postUpdate(Request $request)
    {
        $this->validate($request, [
            'ar_name' => "required|max:250|unique:banks,name,{$request->id},id",
            'en_name' => "required|max:250|unique:banks,en_name,{$request->id},id",
            'status' => 'required',
        ]);
        $dataArr = arrayFromPost(['ar_name', 'en_name', 'status']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $bank = \App\Models\Bank::find($request->id);
            if (!blank($bank)) {
                $bank->name = $dataArr->ar_name;
                $bank->en_name = $dataArr->en_name;
                $bank->status = $dataArr->status;
                $bank->save();
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
        abort_unless(hasPermission('admin.banks.delete'), 401);

        \App\Models\Bank::where('id', $request->id)->delete();
        return successMessage();
    }
}
