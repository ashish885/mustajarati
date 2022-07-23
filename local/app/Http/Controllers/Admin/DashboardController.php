<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class DashboardController extends AdminController
{
    public function getIndex(Request $request)
    {
        navigationMenuListing();

        $admin_amount = 0;
        $admin_amount += \App\Models\ProductBooking::where('is_settled', 1)->sum('admin_amount');
        $admin_amount += \App\Models\ServiceBooking::where('is_settled', 1)->sum('admin_amount');
        $admin_amount = $admin_amount > 1000 ? numberFormatShort($admin_amount) : $admin_amount;

        return view('admin.dashboard.index', compact('admin_amount'));
    }

    public function getStats(Request $request)
    {
        $users = \App\Models\User::count();
        $vendors = \App\Models\Vendor::count();
        $product_bookings = \App\Models\ProductBooking::count();
        $service_bookings = \App\Models\ServiceBooking::count();
        $user_inquiries = \App\Models\Inquiry::where('type', 1)->count();
        $vendor_inquiries = \App\Models\Inquiry::where('type', 2)->count();
        $open_disputes = \App\Models\Dispute::where('status', '<>', 3)->count();

        return response()->json(compact('users', 'vendors', 'product_bookings', 'service_bookings', 'user_inquiries', 'vendor_inquiries', 'open_disputes'));
    }

    public function getEarningsGraph(Request $request)
    {
        $labels = $bookings = $earnings = [];
        $date_range = getDaysBetweenDates($request->start_date, $request->end_date);

        $product_bookings = \App\Models\ProductBooking::select(\DB::raw('COUNT(id) AS bookings, SUM(admin_amount) AS earning, DATE(created_at) AS label'))
            ->whereBetween(\DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->where('status', 3)
            ->where('is_settled', 1)
            ->get();

        $service_bookings = \App\Models\ServiceBooking::select(\DB::raw('COUNT(id) AS bookings, SUM(admin_amount) AS earning, DATE(created_at) AS label'))
            ->whereBetween(\DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->where('status', 3)
            ->where('is_settled', 1)
            ->get();

        $product_bookings = [
            'count' => $product_bookings->pluck('bookings', 'label')->toArray(),
            'sum' => $product_bookings->pluck('earning', 'label')->toArray()
        ];

        $service_bookings = [
            'count' => $service_bookings->pluck('bookings', 'label')->toArray(),
            'sum' => $service_bookings->pluck('earning', 'label')->toArray()
        ];

        // dd(compact('product_bookings', 'service_bookings'));

        foreach ($date_range as $date) {
            $labels[] = date('Y') == date('Y', strtotime($date)) ? date('d-M', strtotime($date)) : date('d-M-y', strtotime($date));
            $bookings[] = (int) @$product_bookings['count'][$date] + (int) @$service_bookings['count'][$date];
            $earnings[] = (int) @$product_bookings['sum'][$date] + (int) @$service_bookings['sum'][$date];
        }

        return response()->json(compact('labels', 'bookings', 'earnings'));
    }

    public function getProductBookingsGraph(Request $request)
    {
        $labels = $stats = [];
        $date_range = getDaysBetweenDates($request->start_date, $request->end_date);

        $result = \App\Models\ProductBooking::select(\DB::raw('COUNT(id) AS bookings, DATE(created_at) AS label'))
            ->whereBetween(\DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->pluck('bookings', 'label')
            ->toArray();

        foreach ($date_range as $date) {
            $labels[] = date('Y') == date('Y', strtotime($date)) ? date('d-M', strtotime($date)) : date('d-M-y', strtotime($date));
            $stats[] = (int) @$result[$date];
        }

        return response()->json(compact('labels', 'stats'));
    }

    public function getServiceBookingsGraph(Request $request)
    {
        $labels = $stats = [];
        $date_range = getDaysBetweenDates($request->start_date, $request->end_date);

        $result = \App\Models\ServiceBooking::select(\DB::raw('COUNT(id) AS bookings, DATE(created_at) AS label'))
            ->whereBetween(\DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->pluck('bookings', 'label')
            ->toArray();

        foreach ($date_range as $date) {
            $labels[] = date('Y') == date('Y', strtotime($date)) ? date('d-M', strtotime($date)) : date('d-M-y', strtotime($date));
            $stats[] = (int) @$result[$date];
        }

        return response()->json(compact('labels', 'stats'));
    }

    public function getUsersGraph(Request $request)
    {
        $labels = $stats = [];
        $date_range = getDaysBetweenDates($request->start_date, $request->end_date);

        $result = \App\Models\User::select(\DB::raw('COUNT(id) AS users, DATE(created_at) AS label'))
            ->whereBetween(\DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->pluck('users', 'label')
            ->toArray();

        foreach ($date_range as $date) {
            $labels[] = date('Y') == date('Y', strtotime($date)) ? date('d-M', strtotime($date)) : date('d-M-y', strtotime($date));
            $stats[] = (int) @$result[$date];
        }

        return response()->json(compact('labels', 'stats'));
    }

    public function getVendorsGraph(Request $request)
    {
        $labels = $stats = [];
        $date_range = getDaysBetweenDates($request->start_date, $request->end_date);

        $result = \App\Models\Vendor::select(\DB::raw('COUNT(id) AS vendors, DATE(created_at) AS label'))
            ->whereBetween(\DB::raw('DATE(created_at)'), [$request->start_date, $request->end_date])
            ->groupBy(\DB::raw('DATE(created_at)'))
            ->pluck('vendors', 'label')
            ->toArray();

        foreach ($date_range as $date) {
            $labels[] = date('Y') == date('Y', strtotime($date)) ? date('d-M', strtotime($date)) : date('d-M-y', strtotime($date));
            $stats[] = (int) @$result[$date];
        }

        return response()->json(compact('labels', 'stats'));
    }

    public function getChangeLocale(Request $request)
    {
        \Session::put('lang', $request->lang);
        return redirect()->back();
    }

    public function getAddressPicker(Request $request)
    {
        $latitude = $request->latitude;
        $longitude = $request->longitude;

        return view('admin.dashboard.address-picker', compact('latitude', 'longitude'));
    }
}
