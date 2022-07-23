<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class CategoryController extends AdminController
{
    protected $img_width = 512;
    protected $img_height = 512;

    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.categories.index'), 401);

        return view('admin.categories.index');
    }

    public function getList(Request $request)
    {
        $categories = \App\Models\Category::select(\DB::raw("categories.*, categories.{$this->ql}name AS name, parent_categories.{$this->ql}name AS parent_category"))
            ->leftJoin('categories AS parent_categories', 'parent_categories.id', '=', 'categories.parent_id');

        return \DataTables::of($categories)
            ->addColumn('type_text', function ($query) {
                return transLang('category_types')[$query->type];
            })
            ->addColumn('status_text', function ($query) {
                return transLang('action_status')[$query->status];
            })
            ->make();
    }

    public function getSubCategoriesList(Request $request)
    {
        $list = \App\Models\Category::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where('parent_id', $request->id)
            ->get();
        return response()->json($list);
    }

    public function getCreate(Request $request)
    {
        abort_unless(hasPermission('admin.categories.create'), 401);

        $img_width = $this->img_width;
        $img_height = $this->img_height;

        $categories = \App\Models\Category::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where('status', 1)
            ->whereNull('parent_id')
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.categories.create', compact('categories', 'img_width', 'img_height'));
    }

    public function postCreate(Request $request)
    {
        $this->validate($request, [
            'category_for' => 'required',
            'parent_category' => 'nullable',
            'ar_name' => 'required|max:250',
            'en_name' => 'required|max:250',
            'image' => 'required',
            'status' => 'required',
        ]);
        $dataArr = arrayFromPost(['category_for', 'parent_category', 'ar_name', 'en_name', 'image', 'status']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $category = new \App\Models\Category();
            $category->type = $dataArr->category_for;
            $category->parent_id = $dataArr->parent_category;
            $category->name = $dataArr->ar_name;
            $category->en_name = $dataArr->en_name;
            $category->status = $dataArr->status;
            $category->image = saveBase64File([
                'width' => $this->img_width,
                'height' => $this->img_height,
                'data_url' => $dataArr->image,
            ]);
            $category->save();

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
        abort_unless(hasPermission('admin.categories.update'), 401);

        $category = \App\Models\Category::findOrFail($request->id);

        $img_width = $this->img_width;
        $img_height = $this->img_height;

        $categories = \App\Models\Category::select(\DB::raw("id, {$this->ql}name AS name"))
            ->where(function ($query) use ($category) {
                $query->where('status', 1);
                if (blank($category->parent_id)) {
                    $query->where('id', '<>', $category->id);
                } else {
                    $query->orWhere('id', $category->parent_id);
                }
            })
            ->whereNull('parent_id')
            ->orderBy("{$this->ql}name")
            ->get();

        return view('admin.categories.update', compact('category', 'categories', 'img_width', 'img_height'));
    }

    public function postUpdate(Request $request)
    {
        $this->validate($request, [
            'category_for' => 'required',
            'parent_category' => 'nullable',
            'ar_name' => 'required|max:250',
            'en_name' => 'required|max:250',
            'image' => 'nullable',
            'status' => 'required',
        ]);
        $dataArr = arrayFromPost(['category_for', 'parent_category', 'ar_name', 'en_name', 'image', 'status']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $category = \App\Models\Category::find($request->id);
            if (!blank($category)) {
                $category->type = $dataArr->category_for;
                $category->parent_id = $dataArr->parent_category;
                $category->name = $dataArr->ar_name;
                $category->en_name = $dataArr->en_name;
                $category->status = $dataArr->status;
                if (!blank($dataArr->image)) {
                    $category->image = saveBase64File([
                        'width' => $this->img_width,
                        'height' => $this->img_height,
                        'data_url' => $dataArr->image,
                    ]);
                }
                $category->save();

                if (!blank($category->parent_id)) {
                    \App\Models\Product::where('sub_category_id', $category->id)->update(['category_id' => $category->parent_id]);
                    \App\Models\Service::where('sub_category_id', $category->id)->update(['category_id' => $category->parent_id]);
                }
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
        abort_unless(hasPermission('admin.categories.delete'), 401);

        $category = \App\Models\Category::where('id', $request->id)->delete();
        return successMessage();
    }
}
