<?php

namespace App\Http\Controllers\API\User\V1;

use App\Http\Controllers\API\User\UserController;
use Illuminate\Http\Request;

class ServiceController extends UserController
{
    public function getList(Request $request)
    {
        $this->validate($request, [
            'cities' => 'nullable|array',
            'cities.*' => 'required|distinct|numeric|exists:cities,id',
            'categories' => 'nullable|array',
            'categories.*' => 'nullable|distinct|numeric|exists:categories,id,type,2',
            'price' => 'nullable|numeric|gt:0',
            'rating' => 'nullable|numeric|gt:0|lte:5',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'page' => 'required|numeric|min:1',
        ]);
        $dataArr = arrayFromPost(['cities', 'categories', 'price', 'rating', 'latitude', 'longitude']);

        try {
            $list = \App\Models\Service::select(\DB::raw("services.id, services.default_image, services.{$this->ql}name AS name, services.avg_rating, services.total_ratings, services.amount, services.amount_type"))
                ->when(is_array($dataArr->cities) && count($dataArr->cities), function ($query) use ($dataArr) {
                    $query->join('service_cities', 'service_cities.service_id', '=', 'services.id')
                        ->whereIn('service_cities.city_id', $dataArr->cities)
                        ->orderBy('services.total_bookings', 'DESC');
                })
                ->when((!blank($dataArr->latitude) && !blank($dataArr->longitude)), function ($query) use ($dataArr) {
                    $query->orderBy(\DB::raw("(((acos(sin(({$dataArr->latitude}*pi()/180)) * sin((services.latitude*pi()/180))+cos(({$dataArr->latitude}*pi()/180)) * cos((services.latitude*pi()/180)) * cos((({$dataArr->longitude}- services.longitude)*pi()/180))))*180/pi())*60*1.1515*1.609344)")); // KM
                })
                ->when(is_array($dataArr->categories) && count($dataArr->categories), function ($query) use ($dataArr) {
                    $query->whereIn('services.category_id', $dataArr->categories);
                })
                ->when(!blank($dataArr->price), function ($query) use ($dataArr) {
                    $query->where('services.daily_amount', '<=', $dataArr->price);
                })
                ->when(!blank($dataArr->rating), function ($query) use ($dataArr) {
                    $query->where('services.avg_rating', '>=', $dataArr->rating);
                })
                ->where('services.status', 1)
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
                ->where('type', 2)
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
            'id' => 'required|numeric|exists:services,id',
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $service = \App\Models\Service::select(\DB::raw("services.id, services.category_id, services.sub_category_id, services.{$this->ql}name AS name, services.total_bookings, services.total_ratings, services.avg_rating, services.daily_amount, services.address, services.latitude, services.longitude, services.{$this->ql}description AS description, services.amount, services.amount_type, services.type, services.visiting_charges"))
                ->find($dataArr->id);
            if (!blank($service)) {
                $service->category = \App\Models\Category::where('id', $service->category_id)->value("{$this->ql}name");
                $service->sub_category = \App\Models\Category::where('id', $service->sub_category_id)->value("{$this->ql}name");

                $service->images = \App\Models\ServiceImage::where('service_id', $service->id)
                    ->orderBy('priority')
                    ->pluck('image');

                $service->cities = \App\Models\City::join('service_cities', 'service_cities.city_id', '=', 'cities.id')
                    ->where('service_cities.service_id', $service->id)
                    ->pluck("cities.{$this->ql}name")
                    ->implode(', ');
            }

            return apiResponse('success', $service);
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
            $list = \App\Models\Service::select(\DB::raw("services.id, services.default_image, services.{$this->ql}name AS name, services.avg_rating, services.total_ratings, services.amount, services.amount_type"))
                ->join('favorite_services', 'favorite_services.service_id', '=', 'services.id')
                ->where('favorite_services.user_id', $user->id)
                ->where('services.status', 1)
                ->orderBy('services.total_bookings', 'DESC')
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
            'id' => 'required|numeric|exists:services,id',
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            if (\App\Models\FavoriteService::where('service_id', $dataArr->id)->where('user_id', $user->id)->doesntExist()) {
                $favService = new \App\Models\FavoriteService();
                $favService->user_id = $user->id;
                $favService->service_id = $dataArr->id;
                $favService->save();
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
            'id' => "required|numeric|exists:favorite_services,service_id,user_id,{$user->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            \App\Models\FavoriteService::where('service_id', $dataArr->id)
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
            'id' => 'required|numeric|exists:services,id',
            'page' => 'required|min:1',
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $list = \App\Models\ServiceReview::select(\DB::raw('service_reviews.rating, service_reviews.comments, service_reviews.created_at, users.name, users.profile_image'))
                ->leftJoin('users', 'users.id', '=', 'service_reviews.user_id')
                ->where('service_reviews.service_id', $dataArr->id)
                ->paginate(15);

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }
}
