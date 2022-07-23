<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class ProductBookingController extends AdminController
{
    public function getIndex(Request $request)
    {
        abort_unless(hasPermission('admin.product_bookings.index'), 401);

        $products = \App\Models\Product::select(\DB::raw("id, {$this->ql}name AS name"))
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
        return view('admin.product_booking.index', compact('vendors', 'users', 'products'));
    }

    public function getList(Request $request)
    {
        $list = \App\Models\ProductBooking::select(\DB::raw("product_bookings.*, users.name as user, vendors.name AS vendor, products.{$this->ql}name AS product"))
            ->leftJoin('users', 'users.id', '=', 'product_bookings.user_id')
            ->leftJoin('vendors', 'vendors.id', '=', 'product_bookings.vendor_id')
            ->leftJoin('products', 'products.id', '=', 'product_bookings.product_id')
            ->when(!blank($request->product_id), function ($query) use ($request) {
                $query->where('product_bookings.product_id', $request->product_id);
            })
            ->when(!blank($request->user_id), function ($query) use ($request) {
                $query->where('product_bookings.user_id', $request->user_id);
            })
            ->when(!blank($request->vendor_id), function ($query) use ($request) {
                $query->where('product_bookings.vendor_id', $request->vendor_id);
            })
            ->when(!blank($request->booking_status), function ($query) use ($request) {
                $query->where('product_bookings.status', $request->booking_status);
            })
            ->when(!blank($request->payment_status), function ($query) use ($request) {
                $query->where('product_bookings.payment_status', $request->payment_status);
            })
            ->when(!blank($request->from_date) && !blank($request->to_date), function ($query) use ($request) {
                $query->whereBetween(\DB::raw('DATE(product_bookings.created_at)'), [$request->from_date, $request->to_date]);
            });

        return \DataTables::of($list)
            ->addColumn('payment_status_text', function ($query) {
                return transLang('payment_status_arr')[$query->payment_status];
            })
            ->addColumn('status_text', function ($query) {
                return transLang('product_booking_status_arr')[$query->status];
            })
            ->make();
    }

    public function getView(Request $request)
    {
        abort_unless(hasPermission('admin.product_bookings.view'), 401);

        $booking = \App\Models\ProductBooking::findOrFail($request->id);
        $booking->user = \App\Models\User::where('id', $booking->user_id)->value('name');
        $booking->vendor = \App\Models\Vendor::where('id', $booking->vendor_id)->value('name');
        $booking->details = \App\Models\ProductBookingDetail::where('product_booking_id', $request->id)->first();
        $booking->questions = \App\Models\ProductBookingQuestion::where('product_booking_id', $request->id)->get();
        $booking->user_action = \App\Models\ProductBookingAction::select(\DB::raw("product_booking_actions.*"))
            ->where('product_booking_id', $request->id)
            ->where('action_by', 1)
            ->first();
        $booking->vendor_action = \App\Models\ProductBookingAction::select(\DB::raw("product_booking_actions.*"))
            ->where('product_booking_id', $request->id)
            ->where('action_by', 2)
            ->first();

        return view('admin.product_booking.view', compact('booking'));
    }

    public function getDelete(Request $request)
    {
        abort_unless(hasPermission('admin.product_bookings.delete'), 401);

        \App\Models\ProductBooking::where('id', $request->id)->delete();
        return successMessage();
    }

    public function getCancel(Request $request)
    {
        $id = $request->id;
        return view('admin.product_booking.cancel', compact('id'));
    }

    public function postCancel(Request $request)
    {
        $this->validate($request, [
            'comments' => 'required|max:2000',
        ]);
        $dataArr = arrayFromPost(['comments']);

        try {
            $booking = \App\Models\ProductBooking::find($request->id);
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
            $booking->cancellation_charges = getAppSetting('product_cancellation_charges');
            $booking->cancellation_refund_amount = $booking->paid_amount - $booking->cancellation_charges;
            $booking->save();

            \App\Models\Product::where('id', $booking->product_id)->update(['booking_end_date' => null]);

            // TODO: refund booking amount

            // Trying to send Notification
            \App\Jobs\Notifications\Product\Vendor\BookingCancelledJob::dispatch(compact('booking'));

            // Trying to send email
            \App\Jobs\Emails\Vendor\Product\BookingCancelledJob::dispatch([
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

    public function getProcessRefund(Request $request)
    {
        $booking = \App\Models\ProductBooking::find($request->id);
        return view('admin.product_booking.process-refund', compact('booking'));
    }

    public function postProcessRefund(Request $request)
    {
        $this->validate($request, [
            'damage_charges' => 'required|numeric|min:0',
        ]);
        $dataArr = arrayFromPost(['damage_charges']);

        try {
            // Start Transaction
            \DB::beginTransaction();

            $booking = \App\Models\ProductBooking::find($request->id);
            $booking->damage_charges = $dataArr->damage_charges;
            $booking->is_refund_initiated = 1;
            if ($booking->refundable_amount > $dataArr->damage_charges) {
                $booking->refundable_amount -= $dataArr->damage_charges;
            } else {
                $booking->refundable_amount = 0;
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

    public function getRefundCancellation(Request $request)
    {
        try {
            // Start Transaction
            \DB::beginTransaction();

            $booking = \App\Models\ProductBooking::find($request->id);
            $booking->is_refund_initiated = 1;
            $booking->is_cancelled_amount_refunded = 1;
            if ($booking->refundable_amount > 0) {
                $booking->refundable_amount = $booking->cancellation_refund_amount;
            }
            $booking->save();

            if ($booking->payment_init_id && $booking->cancellation_refund_amount > 0) {
                $response = \App\Helpers\NoonPayment::getInstance()->refund([
                    'order_id' => $booking->payment_init_id,
                    'amount' => $booking->cancellation_refund_amount,
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
