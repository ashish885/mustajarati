<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class ServiceBookingController extends AdminController
{
    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.service_bookings.index'), 401);

        $services = \App\Models\Service::select(\DB::raw("id, {$this->ql}name AS name"))
            ->orderBy("{$this->ql}name")
            ->get();

        $vendors = \App\Models\Vendor::select(\DB::raw('id, CONCAT(name, " (", email, ")") AS name'))
            ->where('is_profile_verified', 1)
            ->orderBy("name")
            ->get();

        $users = \App\Models\User::select(\DB::raw('id, CONCAT(name, " (","+",dial_code,mobile, ")") AS name'))
            ->where('is_profile_verified', 1)
            ->orderBy("name")
            ->get();

        return view('admin.service_bookings.index', compact('vendors', 'users', 'services'));
    }

    public function getList(Request $request)
    {
        $list = \App\Models\ServiceBooking::select(\DB::raw("service_bookings.*, users.name as user_name, services.{$this->ql}name as service_name, vendors.name as vendor_name"))
            ->leftJoin('users', 'users.id', '=', 'service_bookings.user_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'service_bookings.vendor_id')
            ->leftJoin('services', 'services.id', '=', 'service_bookings.service_id')
            ->when(!blank($request->service_id), function ($query) use ($request) {
                $query->where('service_bookings.service_id', $request->service_id);
            })
            ->when(!blank($request->user_id), function ($query) use ($request) {
                $query->where('service_bookings.user_id', $request->user_id);
            })
            ->when(!blank($request->vendor_id), function ($query) use ($request) {
                $query->where('service_bookings.vendor_id', $request->vendor_id);
            })
            ->when(!blank($request->booking_status), function ($query) use ($request) {
                $query->where('service_bookings.status', $request->booking_status);
            })
            ->when(!blank($request->payment_status), function ($query) use ($request) {
                $query->where('service_bookings.payment_status', $request->payment_status);
            })
            ->when(!blank($request->from_date) && !blank($request->to_date), function ($query) use ($request) {
                $query->whereBetween(\DB::raw('DATE(service_bookings.created_at)'), [$request->from_date, $request->to_date]);
            });

        return \DataTables::of($list)
            ->addColumn('payment_status_text', function ($query) {
                return transLang('payment_status_arr')[$query->payment_status];
            })
            ->addColumn('status_text', function ($query) {
                return transLang('service_booking_status_arr')[$query->status];
            })
            ->make();
    }

    public function getView(Request $request)
    {
        abort_unless(hasPermission('admin.service_bookings.view'), 401);

        $booking = \App\Models\ServiceBooking::findOrFail($request->id);
        $booking->vendor = \App\Models\Vendor::where('id', $booking->vendor_id)->value('name');
        $booking->user = \App\Models\User::where('id', $booking->user_id)->value('name');
        $booking->service = \App\Models\ServiceBookingDetail::where('service_booking_id', $booking->id)->first();

        return view('admin.service_bookings.view', compact('booking'));
    }

    public function getDelete(Request $request)
    {
        abort_unless(hasPermission('admin.service_bookings.delete'), 401);

        \App\Models\ServiceBooking::where('id', $request->id)->delete();
        return successMessage();
    }

    public function getCancel(Request $request)
    {
        $id = $request->id;
        return view('admin.service_bookings.cancel', compact('id'));
    }

    public function postCancel(Request $request)
    {
        $this->validate($request, [
            'comments' => 'required|max:2000',
        ]);
        $dataArr = arrayFromPost(['comments']);

        try {
            $booking = \App\Models\ServiceBooking::find($request->id);
            if ($booking->status == 5) {
                return errorMessage('booking_already_cancelled');
            } elseif ($booking->status > 2) {
                return errorMessage('action_not_allowed');
            }

            // Start Transaction
            \DB::beginTransaction();

            $booking->status = 5;
            $booking->cancellation_reason = $dataArr->comments;
            $booking->en_cancellation_reason = $dataArr->comments;
            $booking->cancellation_charges = getAppSetting('service_cancellation_charges');
            $booking->cancellation_refund_amount = $booking->paid_amount - $booking->cancellation_charges;
            $booking->save();

            // TODO: refund booking amount

            // Trying to send Notification
            \App\Jobs\Notifications\Service\Vendor\BookingCancelledJob::dispatch(compact('booking'));

            // Trying to send email
            \App\Jobs\Emails\Vendor\Service\BookingCancelledJob::dispatch([
                'locale' => $this->locale,
                'booking' => $booking,
            ]);

            // Commit Transaction
            \DB::commit();

            return successMessage();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function getRefundAmount(Request $request)
    {
        try {
            // Start Transaction
            \DB::beginTransaction();

            $booking = \App\Models\ServiceBooking::find($request->id);
            $booking->is_refund_initiated = 1;
            if ($booking->status != 3) {
                $booking->is_cancelled_amount_refunded = 1;
                if ($booking->refundable_amount > 0) {
                    $booking->refundable_amount = $booking->cancellation_refund_amount;
                }

            }
            $booking->save();

            if ($booking->payment_init_id && $booking->refundable_amount > 0) {
                $response = \App\Helpers\NoonPayment::getInstance()->refund([
                    'order_id' => $booking->payment_init_id,
                    'amount' => $booking->refundable_amount,
                ]);
                if ($response->err) {
                    // Rollback Transaction
                    \DB::rollBack();
                    return errorMessage($response->message, true);
                }
            }

            // Commit Transaction
            \DB::commit();

            return successMessage('refund_process_initiated');
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            dd($e);
            return exceptionErrorMessage($e);
        }
    }
}
