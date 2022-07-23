<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class DashboardController extends VendorController
{
    public function getDashboardStats(Request $request)
    {
        $vendor = getTokenUser();
        try {
            // Product Booking => 1.Pending, 2.Ongoing, 3.Completed, 4.Rejected, 5.Cancelled
            // Service Booking => 1.Pending, 2.Ongoing, 3.Completed, 4.Rejected, 5.Cancelled

            $output = new \stdClass;
            $output->android_app_version = getAppSetting('android_vendor_app_version');
            $output->ios_app_version = getAppSetting('ios_vendor_app_version');

            $output->stats = new \stdClass;
            $output->stats->ongoing = (\App\Models\ProductBooking::where('vendor_id', $vendor->id)->where('status', 2)->count()+\App\Models\ServiceBooking::where('vendor_id', $vendor->id)->where('status', 2)->count());
            $output->stats->cancelled = (\App\Models\ProductBooking::where('vendor_id', $vendor->id)->whereIn('status', [4, 5])->count()+\App\Models\ServiceBooking::where('vendor_id', $vendor->id)->whereIn('status', [5, 6])->count());
            $output->stats->completed = (\App\Models\ProductBooking::where('vendor_id', $vendor->id)->where('status', 3)->count()+\App\Models\ServiceBooking::where('vendor_id', $vendor->id)->where('status', 3)->count());

            $output->total_earnings = 0;
            $output->earning_graph = [];
            for ($i = 0; $i < 6; $i++) {
                $start_date = date('Y-m-01', strtotime("-{$i} MONTH"));
                $end_date = date('Y-m-t', strtotime("-{$i} MONTH"));

                $total_bookings = \App\Models\ProductBooking::whereVendorId($vendor->id)
                    ->whereStatus(3)
                    ->whereBetween(\DB::raw('DATE(created_at)'), [$start_date, $end_date])
                    ->count()
                +
                \App\Models\ServiceBooking::whereVendorId($vendor->id)
                    ->whereStatus(3)
                    ->whereBetween(\DB::raw('DATE(created_at)'), [$start_date, $end_date])
                    ->count();

                $total_earning = \App\Models\ProductBooking::whereVendorId($vendor->id)
                    ->whereStatus(3)
                    ->whereBetween(\DB::raw('DATE(created_at)'), [$start_date, $end_date])
                    ->sum('vendor_amount')
                +
                \App\Models\ServiceBooking::whereVendorId($vendor->id)
                    ->whereStatus(3)
                    ->whereBetween(\DB::raw('DATE(created_at)'), [$start_date, $end_date])
                    ->sum('vendor_amount');

                $output->total_earnings += $total_earning;
                $total_earning_string = numberFormatShort($total_earning);
                $total_earning = (string) $total_earning;

                $month = date('m', strtotime($start_date));
                $date = date('Y-m', strtotime($start_date));
                $output->earning_graph[] = compact('total_bookings', 'total_earning', 'total_earning_string', 'month', 'date');
            }

            $output->total_earnings_string = numberFormatShort($output->total_earnings);
            $output->total_earnings = (string) round(array_sum(array_column($output->earning_graph, 'total_earning')), 2);

            $output->unread_notifications = \App\Models\Notification::where('vendor_id', $vendor->id)
                ->where('is_read', 0)
                ->count();

            $output->have_products = (int) \App\Models\Product::where('vendor_id', $vendor->id)->exists();
            $output->have_services = (int) \App\Models\Service::where('vendor_id', $vendor->id)->exists();

            $output->rented_products = [];
            $output->rented_services = [];
            if ($output->have_products) {
                $output->rented_products = \App\Models\ProductBooking::select(\DB::raw("product_bookings.id, product_bookings.booking_code, product_bookings.no_of_days, product_bookings.no_of_hours, product_bookings.to_date, product_bookings.created_at, product_bookings.payment_status, product_bookings.status, product_booking_details.image, product_booking_details.{$this->ql}name AS product_name, CONCAT(users.dial_code, users.mobile) AS mobile_no, 'product' AS booking_type"))
                    ->join('product_booking_details', 'product_booking_details.product_booking_id', '=', 'product_bookings.id')
                    ->leftJoin('users', 'users.id', '=', 'product_bookings.user_id')
                    ->where('product_bookings.vendor_id', $vendor->id)
                    ->whereIn('product_bookings.status', [1, 2, 6])
                    ->orderBy('product_bookings.id', 'desc')
                    ->take(5)
                    ->get();

            }
            if ($output->have_services) {
                $output->rented_services = \App\Models\ServiceBooking::select(\DB::raw("service_bookings.id, service_bookings.booking_code, service_bookings.to_date, service_bookings.no_of_days, service_bookings.no_of_hours, service_bookings.payment_status, service_bookings.status, service_bookings.created_at, service_booking_details.image, service_booking_details.{$this->ql}name AS service_name, CONCAT(users.dial_code, users.mobile) AS mobile_no, 'service' AS booking_type"))
                    ->join('service_booking_details', 'service_booking_details.service_booking_id', '=', 'service_bookings.id')
                    ->leftJoin('users', 'users.id', '=', 'service_bookings.user_id')
                    ->where('service_bookings.vendor_id', $vendor->id)
                    ->whereIn('service_bookings.status', [1, 2, 6])
                    ->orderBy('service_bookings.id', 'desc')
                    ->take(5)
                    ->get();
            }

            return apiResponse('success', $output);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getAppSettings(Request $request)
    {
        $vendor = getTokenUser();
        try {
            $output = new \stdClass;
            $output->android_app_version = getAppSetting('android_vendor_app_version');
            $output->ios_app_version = getAppSetting('ios_vendor_app_version');
            $output->is_security_amount_enabled = getAppSetting('is_security_amount_enabled');

            return apiResponse('success', $output);
        } catch (\Exception $e) {
            return errorMessage($e->getMessage(), true);
        }
    }

    public function postUploadProductOrServiceImage(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            // 'image' => 'required|' . config('cms.allowed_image_mimes') . '|dimensions:width=512,height=512',
            'image' => 'required|' . config('cms.allowed_image_mimes'),
        ]);

        try {
            $image = uploadFile('image');

            return apiResponse('success', compact('image'));
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }
}
