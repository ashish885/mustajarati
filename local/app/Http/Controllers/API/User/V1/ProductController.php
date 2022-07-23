<?php

namespace App\Http\Controllers\API\User\V1;

use App\Http\Controllers\API\User\UserController;
use Illuminate\Http\Request;

class ProductController extends UserController
{
    public function getList(Request $request)
    {
        $this->validate($request, [
            'cities' => 'nullable|array',
            'cities.*' => 'required|distinct|numeric|exists:cities,id',
            'category_id' => 'nullable|numeric|exists:categories,id',
            'sub_categories' => "nullable|array",
            'sub_categories.*' => "required|distinct|numeric|exists:categories,id,parent_id,{$request->category_id}",
            'price' => 'nullable|numeric|gt:0',
            'rating' => 'nullable|numeric|gt:0|lte:5',
            'is_new' => 'nullable|in:all,new,old',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'page' => 'required|numeric|min:1',
        ]);
        $dataArr = arrayFromPost(['cities', 'category_id', 'sub_categories', 'price', 'rating', 'is_new', 'latitude', 'longitude']);

        try {
            $list = \App\Models\Product::select(\DB::raw("products.id, products.default_image, products.{$this->ql}name AS name, products.avg_rating, products.total_ratings, products.amount, products.amount_type, GROUP_CONCAT(cities.{$this->ql}name SEPARATOR ', ') AS cities, CASE WHEN products.booking_end_date IS NULL THEN 1 ELSE 0 END AS is_available"))
                ->leftJoin('product_cities', 'product_cities.product_id', '=', 'products.id')
                ->leftJoin('cities', 'cities.id', '=', 'product_cities.city_id')
                ->when(is_array($dataArr->cities) && count($dataArr->cities), function ($query) use ($dataArr) {
                    $query->whereIn('product_cities.city_id', $dataArr->cities)
                        ->orderBy('products.total_bookings', 'DESC');
                })
                ->when((!blank($dataArr->latitude) && !blank($dataArr->longitude)), function ($query) use ($dataArr) {
                    $query->orderBy(\DB::raw("(((acos(sin(({$dataArr->latitude}*pi()/180)) * sin((products.latitude*pi()/180))+cos(({$dataArr->latitude}*pi()/180)) * cos((products.latitude*pi()/180)) * cos((({$dataArr->longitude}- products.longitude)*pi()/180))))*180/pi())*60*1.1515*1.609344)")); // KM
                })
                ->when(!blank($dataArr->category_id), function ($query) use ($dataArr) {
                    $query->where('products.category_id', $dataArr->category_id);
                })
                ->when(is_array($dataArr->sub_categories) && count($dataArr->sub_categories), function ($query) use ($dataArr) {
                    $query->whereIn('products.sub_category_id', $dataArr->sub_categories);
                })
                ->when(!blank($dataArr->price), function ($query) use ($dataArr) {
                    $query->where('products.amount', '<=', $dataArr->price);
                })
                ->when(!blank($dataArr->rating), function ($query) use ($dataArr) {
                    $query->where('products.avg_rating', '>=', $dataArr->rating);
                })
                ->when(in_array($dataArr->is_new, ['new', 'old']), function ($query) use ($dataArr) {
                    $query->where('products.is_new', ($dataArr->is_new == 'new' ? 1 : 0));
                })
                ->where('products.status', 1)
                ->groupBy('products.id')
                ->paginate(10);

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getFilterData(Request $request)
    {
        try {
            $response = new \stdClass;

            $response->cities = \App\Models\City::select(\DB::raw("id, {$this->ql}name as name, latitude, longitude"))
                ->where('status', 1)
                ->orderBy("{$this->ql}name")
                ->get();

            $response->categories = \App\Models\Category::select(\DB::raw("id, image, {$this->ql}name AS name"))
                ->where('type', 1)
                ->whereNull('parent_id')
                ->where('status', 1)
                ->get();

            return apiResponse('success', $response);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getDetails(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric|exists:products,id',
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $product = \App\Models\Product::select(\DB::raw("products.id, products.category_id, products.sub_category_id, products.{$this->ql}name AS name, products.total_bookings, products.total_ratings, products.avg_rating, products.amount, products.amount_type, products.daily_amount, products.is_new, products.location, products.latitude, products.longitude, products.security_amount, products.delay_charges, products.delay_charges_type, products.{$this->ql}description AS description, products.{$this->ql}features AS features, products.is_delivery_available, products.delivery_charges, CASE WHEN products.booking_end_date IS NULL THEN 1 ELSE 0 END AS is_available"))
                ->find($dataArr->id);
            if (!blank($product)) {
                $product->category = \App\Models\Category::where('id', $product->category_id)->value("{$this->ql}name");
                $product->sub_category = \App\Models\Category::where('id', $product->sub_category_id)->value("{$this->ql}name");
                $product->features = @unserialize($product->features);

                $product->images = \App\Models\ProductImage::where('product_id', $product->id)
                    ->orderBy('priority')
                    ->pluck('image');

                $product->cities = \App\Models\City::join('product_cities', 'product_cities.city_id', '=', 'cities.id')
                    ->where('product_cities.product_id', $product->id)
                    ->pluck("cities.{$this->ql}name")
                    ->implode(', ');
            }

            return apiResponse('success', $product);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getFavoritesList(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'page' => 'required|numeric|min:1',
        ]);

        try {
            $list = \App\Models\Product::select(\DB::raw("products.id, products.default_image, products.{$this->ql}name AS name, products.avg_rating, products.total_ratings, products.amount, products.amount_type, GROUP_CONCAT(cities.{$this->ql}name SEPARATOR ', ') AS cities, CASE WHEN products.booking_end_date IS NULL THEN 1 ELSE 0 END AS is_available"))
                ->join('favorite_products', 'favorite_products.product_id', '=', 'products.id')
                ->leftJoin('product_cities', 'product_cities.product_id', '=', 'products.id')
                ->leftJoin('cities', 'cities.id', '=', 'product_cities.city_id')
                ->where('favorite_products.user_id', $user->id)
                ->where('products.status', 1)
                ->orderBy('products.total_bookings', 'DESC')
                ->groupBy('products.id')
                ->paginate(10);

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postAddFavorite(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'id' => 'required|numeric|exists:products,id',
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            if (\App\Models\FavoriteProduct::where('product_id', $dataArr->id)->where('user_id', $user->id)->doesntExist()) {
                $favProduct = new \App\Models\FavoriteProduct();
                $favProduct->user_id = $user->id;
                $favProduct->product_id = $dataArr->id;
                $favProduct->save();
            }

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postRemoveFavorite(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:favorite_products,product_id,user_id,{$user->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            \App\Models\FavoriteProduct::where('product_id', $dataArr->id)
                ->where('user_id', $user->id)
                ->delete();

            return apiResponse();
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getReviewsList(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric|exists:products,id',
            'page' => 'required|min:1',
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $list = \App\Models\ProductReview::select(\DB::raw('product_reviews.rating, product_reviews.comments, product_reviews.created_at, users.name, users.profile_image'))
                ->leftJoin('users', 'users.id', '=', 'product_reviews.user_id')
                ->where('product_reviews.product_id', $dataArr->id)
                ->paginate(15);

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }
}
