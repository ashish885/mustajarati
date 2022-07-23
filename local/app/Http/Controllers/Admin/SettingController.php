<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class SettingController extends AdminController
{
    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.settings.index'), 401);

        $settings = \App\Models\Setting::orderBy('display_order')->get();
        return view('admin.settings.index', compact('settings'));
    }

    public function postUpdate(Request $request)
    {
        $this->validate($request, [
            'field' => 'required|array',
            'field.*.*' => 'required',
        ]);
        $dataArr = arrayFromPost(['field']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            if (is_array($dataArr->field) && count($dataArr->field)) {
                foreach ($dataArr->field as $id => $row) {
                    $setting = \App\Models\Setting::find($id);
                    if ($setting) {
                        if ($setting->is_file == 0) {
                            if ($setting->is_single) {
                                $setting->value = $dataArr->{"field"}[$id][$setting->attribute];
                                $setting->en_value = $dataArr->{"field"}[$id][$setting->attribute];
                            } else {
                                $setting->value = $dataArr->{"field"}[$id]["ar_{$setting->attribute}"];
                                $setting->en_value = $dataArr->{"field"}[$id]["en_{$setting->attribute}"];
                            }
                            $setting->save();
                        }
                    }
                }
            }

            $settingFiles = \App\Models\Setting::where('is_file', 1)->get();
            if ($settingFiles->isNotEmpty()) {
                foreach ($settingFiles as $setting) {
                    if ($request->{$setting->attribute}) {
                        $filename = uploadFile($setting->attribute);
                        if ($filename) {
                            $setting->value = $filename;
                            $setting->en_value = $filename;
                            $setting->save();
                        } else {
                            \DB::rollBack();
                            return errorMessage('file_uploading_failed');
                        }
                    }
                }
            }

            // Commit Transaction
            \DB::commit();

            \Cache::forget('settings');

            return successMessage();
        } catch (\Throwable $th) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($th);
        }
    }
}
