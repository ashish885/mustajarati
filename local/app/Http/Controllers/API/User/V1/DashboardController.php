<?php

namespace App\Http\Controllers\API\User\V1;

use App\Http\Controllers\API\User\UserController;
use Illuminate\Http\Request;

class DashboardController extends UserController
{
    public function getDashboardStats(Request $request)
    {
        $user = getTokenUser(false);
        try {
            // Product Booking => 1.Pending, 2.Ongoing, 3.Completed, 4.Rejected, 5.Cancelled
            // Service Booking => 1.Pending, 2.Accepted, 3.Ongoing, 4.Completed, 5.Rejected, 6.Cancelled

            $response = new \stdClass;
            $response->tax_percentage = getAppSetting('tax_percentage');
            $response->delivery_charges = getAppSetting('delivery_charges');
            $response->product_cancellation_charges = getAppSetting('product_cancellation_charges');
            $response->service_cancellation_charges = getAppSetting('service_cancellation_charges');
            $response->android_app_version = getAppSetting('android_user_app_version');
            $response->ios_app_version = getAppSetting('ios_user_app_version');

            $response->banners = \App\Models\Banner::pluck("{$this->ql}image");

            $response->categories = \App\Models\Category::select(\DB::raw("id, image, {$this->ql}name AS name"))
                ->whereNull('parent_id')
                ->where('status', 1)
                ->where('type', 1)
                ->orderBy("{$this->ql}name")
                ->get();

            $response->service_categories = \App\Models\Category::select(\DB::raw("id, image, {$this->ql}name AS name"))
                ->whereNull('parent_id')
                ->where('status', 1)
                ->where('type', 2)
                ->orderBy("{$this->ql}name")
                ->get();

            $response->unread_notifications = 0;
            $response->recent_services = $response->recent_products = $response->favorite_products = $response->favorite_services = [];

            if (!blank($user)) {
                $response->unread_notifications = \App\Models\Notification::where('user_id', $user->id)
                    ->where('is_read', 0)
                    ->count();

                $response->recent_products = \App\Models\ProductBooking::select(\DB::raw("product_bookings.id, product_bookings.booking_code, product_bookings.no_of_days, product_bookings.no_of_hours, product_bookings.to_date, product_bookings.payment_status, product_bookings.status, product_booking_details.image, product_booking_details.{$this->ql}name AS product_name, CONCAT(vendors.dial_code, vendors.mobile) AS vendor_mobile_no"))
                    ->join('product_booking_details', 'product_booking_details.product_booking_id', '=', 'product_bookings.id')
                    ->leftJoin('vendors', 'vendors.id', '=', 'product_bookings.vendor_id')
                    ->where('product_bookings.user_id', $user->id)
                    ->whereIn('product_bookings.status', [1, 2])
                    ->orderBy('product_bookings.id', 'desc')
                    ->take(5)
                    ->get();

                $response->recent_services = \App\Models\ServiceBooking::select(\DB::raw("service_bookings.id, service_bookings.booking_code, service_bookings.to_date, service_bookings.no_of_days, service_bookings.no_of_hours, service_bookings.payment_status, service_bookings.status, service_booking_details.image, service_booking_details.{$this->ql}name AS service_name, CONCAT(vendors.dial_code, vendors.mobile) AS vendor_mobile_no"))
                    ->join('service_booking_details', 'service_booking_details.service_booking_id', '=', 'service_bookings.id')
                    ->leftJoin('vendors', 'vendors.id', '=', 'service_bookings.vendor_id')
                    ->where('service_bookings.user_id', $user->id)
                    ->whereIn('service_bookings.status', [1, 2])
                    ->orderBy('service_bookings.id', 'desc')
                    ->take(5)
                    ->get();

                $response->favorite_products = \App\Models\FavoriteProduct::where('user_id', $user->id)->pluck('product_id');

                $response->favorite_services = \App\Models\FavoriteService::where('user_id', $user->id)->pluck('service_id');
            }

            return apiResponse('success', $response);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getAppSettings(Request $request)
    {
        $vendor = getTokenUser();
        try {
            $output = new \stdClass;
            $output->tax_percentage = getAppSetting('tax_percentage');
            $output->delivery_charges = getAppSetting('delivery_charges');
            $output->product_cancellation_charges = getAppSetting('product_cancellation_charges');
            $output->service_cancellation_charges = getAppSetting('service_cancellation_charges');
            $output->android_app_version = getAppSetting('android_user_app_version');
            $output->ios_app_version = getAppSetting('ios_user_app_version');
            $output->is_security_amount_enabled = getAppSetting('is_security_amount_enabled');

            return apiResponse('success', $output);
        } catch (\Exception $e) {
            return errorMessage($e->getMessage(), true);
        }
    }
}
