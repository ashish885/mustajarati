<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class ServiceBookingController extends VendorController
{
    public function getListing(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'page' => 'required|min:1',
        ]);

        try {
            $list = \App\Models\ServiceBooking::select(\DB::raw("service_bookings.id, service_bookings.booking_code, service_bookings.from_date, service_bookings.to_date, service_bookings.no_of_days, service_bookings.no_of_hours, service_bookings.payment_status, service_bookings.total_amount, service_bookings.status, service_bookings.created_at, service_bookings.service_review_id, service_reviews.rating, service_booking_details.image, service_booking_details.{$this->ql}name AS service, service_booking_details.{$this->ql}category_name AS category, service_booking_details.{$this->ql}sub_category_name AS sub_category, CONCAT(users.dial_code, users.mobile) AS vendor_mobile_no, service_booking_details.amount, service_booking_details.amount_type"))
                ->join('service_booking_details', 'service_booking_details.service_booking_id', '=', 'service_bookings.id')
                ->leftJoin('service_reviews', 'service_reviews.id', '=', 'service_bookings.service_review_id')
                ->leftJoin('users', 'users.id', '=', 'service_bookings.user_id')
                ->where('service_bookings.vendor_id', $vendor->id)
                ->orderBy('service_bookings.id', 'desc')
                ->paginate(10);

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getDetails(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:service_bookings,id,vendor_id,{$vendor->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $booking = \App\Models\ServiceBooking::select(\DB::raw("id, booking_code, from_date, to_date, no_of_days, no_of_hours, subtotal, visiting_charges, tax_percentage, tax_amount, total_amount, is_cancelled_amount_refunded, cancellation_refund_amount, cancellation_refund_at, payment_status, status, vendor_id, service_review_id, {$this->ql}cancellation_reason AS cancellation_reason, user_id, vendor_amount, dispute_end_date, vendor_otp, created_at"))
                ->find($dataArr->id);
            if (!blank($booking)) {
                $booking->service = \App\Models\ServiceBookingDetail::select(\DB::raw("id, image, name, en_name, service_type, address, latitude, longitude, user_address, user_latitude, user_longitude, service_booking_details.{$this->ql}category_name AS category, service_booking_details.{$this->ql}sub_category_name AS sub_category, amount, amount_type, {$this->ql}cities AS vendor_cities"))
                    ->where('service_booking_id', $booking->id)
                    ->first();

                $booking->user = null;
                if ($booking->payment_status == 2 && in_array($booking->status, [2, 3])) {
                    $booking->user = \App\Models\User::select(\DB::raw('name, dial_code, mobile'))
                        ->where('id', $booking->user_id)
                        ->first();
                }

                $booking->review = null;
                if (!blank($booking->service_review_id)) {
                    $booking->review = \App\Models\ServiceReview::select(\DB::raw('rating, comments, created_at'))
                        ->where('service_booking_id', $booking->id)
                        ->first();
                }
            }

            return apiResponse('success', $booking);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postAccept(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:service_bookings,id,vendor_id,{$vendor->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $booking = \App\Models\ServiceBooking::find($dataArr->id);
            if ($booking->status > 1) {
                return errorMessage('action_not_allowed');
            }

            // Start Transaction
            \DB::beginTransaction();

            $booking->status = 6;
            $booking->save();

            // Trying to send Notification
            \App\Jobs\Notifications\Service\User\BookingAcceptedJob::dispatch(compact('booking'));

            // Commit Transaction
            \DB::commit();

            return apiResponse();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }


    // 1.Pending, 2.Ongoing, 3.Completed, 4.Rejected, 5.Cancelled
    public function postCancel(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:service_bookings,id,vendor_id,{$vendor->id}",
            'reason' => 'nullable|max:2000',
        ]);
        $dataArr = arrayFromPost(['id', 'reason']);

        try {
            $booking = \App\Models\ServiceBooking::find($dataArr->id);
            if ($booking->status == 5) {
                return errorMessage('booking_already_cancelled');
            } elseif (!in_array($booking->status, [1, 6])) {
                return errorMessage('action_not_allowed');
            }

            // Start Transaction
            \DB::beginTransaction();

            $booking->status = 5;
            $booking->cancellation_reason = $dataArr->reason;
            $booking->en_cancellation_reason = $dataArr->reason;
            $booking->cancellation_refund_amount = $booking->paid_amount;
            $booking->refund_date = $booking->cancellation_refund_amount > 0 ? date('Y-m-d', strtotime('+2 day')) : null;
            $booking->save();

            // Trying to send Notification
            \App\Jobs\Notifications\Service\User\BookingCancelledJob::dispatch(compact('booking'));

            // Trying to send email
            $userEmail = \App\Models\User::where('id', $booking->user_id)->value('email');
            \App\Jobs\Emails\User\Service\BookingRejectedJob::dispatch([
                'email' => $userEmail,
                'locale' => $this->locale,
                'booking' => $booking,
            ]);

            // Try to refund amount
            if ($booking->payment_init_id && $booking->cancellation_refund_amount > 0) {
                $response = \App\Helpers\NoonPayment::getInstance()->refund([
                    'order_id' => $booking->payment_init_id,
                    'amount' => $booking->cancellation_refund_amount,
                ]);
                if (!$response->err) {
                    $booking->is_refund_initiated = 1;
                    $booking->is_cancelled_amount_refunded = 1;
                    $booking->refundable_amount = $booking->cancellation_refund_amount;
                    $booking->save();
                }
            }

            // Commit Transaction
            \DB::commit();

            return apiResponse('success');
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function postStart(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:service_bookings,id,vendor_id,{$vendor->id}",
            'otp' => 'required|digits:4',
        ]);
        $dataArr = arrayFromPost(['id', 'otp']);

        try {
            $booking = \App\Models\ServiceBooking::find($dataArr->id);
            if ($booking->status == 2) {
                return errorMessage('booking_already_started');
            } elseif ($booking->status != 6) {
                return errorMessage('action_not_allowed');
            } elseif ($booking->user_otp != $dataArr->otp) {
                return errorMessage('invalid_booking_otp');
            }

            // Try to Capture Payment
            if (!blank($booking->transaction_id)) {
                // $paymentDataArr = new \stdClass();
                // $paymentDataArr->payment_id = $booking->transaction_id;
                // $paymentDataArr->total_amount = $booking->total_amount;
            }

            // Start Transaction
            \DB::beginTransaction();

            // $booking->transaction_id = @$response['data']->id;
            $booking->status = 2;
            $booking->vendor_otp = generateOtp();
            $booking->user_otp = generateOtp();
            $booking->booking_start_date = date('Y-m-d H:i:s');
            $booking->save();

            // Commit Transaction
            \DB::commit();

            // Trying to send Notification
            \App\Jobs\Notifications\Service\User\BookingStartJob::dispatch(compact('booking'));

            return apiResponse('success');
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function postComplete(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:service_bookings,id,vendor_id,{$vendor->id}",
            'otp' => 'required|digits:4',
        ]);
        $dataArr = arrayFromPost(['id', 'otp']);

        try {
            $booking = \App\Models\ServiceBooking::find($dataArr->id);
            if ($booking->status == 3) {
                return errorMessage('booking_already_completed');
            } elseif ($booking->status != 2) {
                return errorMessage('action_not_allowed');
            } elseif ($booking->user_otp != $dataArr->otp) {
                return errorMessage('invalid_booking_otp');
            }

            // Start Transaction
            \DB::beginTransaction();

            // Calculate Booking Charges Again
            $bookingDetails = \App\Models\ServiceBookingDetail::where('service_booking_id', $booking->id)
                ->first();

            // $calculations = bookingCalculation($booking->booking_start_date, date('Y-m-d H:i:s'), $bookingDetails->amount, $bookingDetails->amount_type, $booking->visiting_charges, 0, $booking->tax_percentage);
            // $total_hours = $calculations['days'] * 24 + $calculations['hours'];

            // $admin_amount = $calculations['total_amount'] * $booking->admin_commission_percent * 0.01;
            // $vendor_amount = $calculations['total_amount'] - $admin_amount;

            // Update Booking
            // $booking->actual_from_date = $booking->from_date;
            // $booking->actual_to_date = $booking->to_date;
            // $booking->from_date = date('Y-m-d H:i:s', strtotime($booking->booking_start_date));
            // $booking->to_date = date('Y-m-d H:i:s');
            // $booking->no_of_days = $calculations['days'];
            // $booking->no_of_hours = $calculations['hours'];
            // $booking->total_hours = $total_hours;
            // $booking->subtotal = $calculations['subtotal'];
            // $booking->tax_amount = $calculations['tax_amount'];
            // $booking->total_amount = $calculations['total_amount'];
            // $booking->admin_amount = $admin_amount;
            // $booking->vendor_amount = $vendor_amount;
            $booking->booking_complete_date = date('Y-m-d H:i:s');
            $booking->dispute_end_date = date('Y-m-d', strtotime("+{$booking->dispute_days} DAY"));
            $booking->completed_by = 2;
            $booking->status = 3;
            $booking->save();

            // Trying to send email
            $userEmail = \App\Models\User::where('id', $booking->user_id)->value('email');
            \App\Jobs\Emails\User\Service\BookingCompletedJob::dispatch([
                'email' => $userEmail,
                'locale' => $this->locale,
                'booking' => $booking,
                'booking_details' => $bookingDetails,
            ]);

            // Commit Transaction
            \DB::commit();

            // Trying to send Notification
            \App\Jobs\Notifications\Service\User\BookingCompletedJob::dispatch(compact('booking'));

            return apiResponse('success');
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }
}
