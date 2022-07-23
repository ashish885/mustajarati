<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    public static function getData()
    {
        $cacheId = 'testimonials';
        if (env('APP_DEBUG')) {
            \Cache::forget($cacheId);
        }

        if (!$data = \Cache::get($cacheId)) {
            $data = Testimonial::select(\DB::raw('testimonials.*, cities.name AS city, cities.en_name AS en_city'))
                ->leftJoin('cities', 'cities.id', '=', 'testimonials.city_id')
                ->orderBy('testimonials.display_order')
                ->get();
            \Cache::forever($cacheId, $data);
        }

        return $data;
    }
}
