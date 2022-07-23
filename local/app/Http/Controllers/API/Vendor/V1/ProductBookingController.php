<?php

namespace App\Http\Controllers\API\Vendor\V1;

use App\Http\Controllers\API\Vendor\VendorController;
use Illuminate\Http\Request;

class ProductBookingController extends VendorController
{
    public function getList(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'page' => 'required|min:0',
        ]);

        try {
            $list = \App\Models\ProductBooking::select(\DB::raw("product_bookings.id, product_bookings.booking_code, product_bookings.no_of_days, product_bookings.no_of_hours, product_bookings.from_date, product_bookings.to_date, product_bookings.total_amount, product_bookings.actual_amount, product_bookings.payment_status, product_bookings.status, product_bookings.created_at, product_bookings.product_review_id, product_reviews.rating, product_booking_details.image, product_booking_details.{$this->ql}name AS product_name, product_booking_details.{$this->ql}category_name AS category, product_booking_details.{$this->ql}sub_category_name AS sub_category, product_booking_details.amount, product_booking_details.amount_type"))
                ->join('product_booking_details', 'product_booking_details.product_booking_id', '=', 'product_bookings.id')
                ->leftJoin('product_reviews', 'product_reviews.id', '=', 'product_bookings.product_review_id')
                ->where('product_bookings.vendor_id', $vendor->id)
                ->orderBy('product_bookings.id', 'desc')
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
            'id' => "required|numeric|exists:product_bookings,id,vendor_id,{$vendor->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $booking = \App\Models\ProductBooking::select(\DB::raw("id, booking_code, from_date, to_date, no_of_days, no_of_hours, subtotal, security_amount_type, security_amount, offline_security_amount, drop_charges, tax_percentage, tax_amount, total_amount, paid_amount, extra_hours, extra_hours_charges, damage_charges, refundable_amount, is_user_received_product, is_user_handed_over_product, is_vendor_received_product, is_vendor_handed_over_product, is_refund_initiated, refund_date, is_user_pending_amount_paid, is_cancelled_amount_refunded, cancellation_refund_amount, cancellation_refund_at, payment_status, status, user_id, product_review_id, {$this->ql}cancellation_reason AS cancellation_reason, vendor_amount, dispute_end_date, otp, created_at"))
                ->find($dataArr->id);
            if (!blank($booking)) {
                $booking->total_extra_charges = (string) ($booking->drop_charges + $booking->damage_charges);
                // $booking->total_extra_charges = (string) ($booking->extra_hours_charges + $booking->drop_charges + $booking->damage_charges);

                $booking->product = \App\Models\ProductBookingDetail::select(\DB::raw("id, image, name, en_name, pickup_type, drop_location, drop_latitude, drop_longitude, pickup_location, pickup_latitude, pickup_longitude, {$this->ql}category_name AS category, {$this->ql}sub_category_name AS sub_category, amount, amount_type, {$this->ql}cities AS vendor_cities"))
                    ->where('product_booking_id', $booking->id)
                    ->first();

                $booking->user = null;
                if ($booking->payment_status == 2 && in_array($booking->status, [6, 2, 3])) {
                    $booking->user = \App\Models\User::select(\DB::raw('name, dial_code, mobile'))
                        ->where('id', $booking->user_id)
                        ->first();
                }

                $booking->review = null;
                if (!blank($booking->product_review_id)) {
                    $booking->review = \App\Models\ProductReview::select(\DB::raw('rating, comments, created_at'))
                        ->where('product_booking_id', $booking->id)
                        ->first();
                }

                $answered_questions = ['by_user' => [], 'by_vendor' => []];
                if (in_array($booking->status, [2, 3])) {
                    $answered_questions['by_user'] = \App\Models\ProductBookingQuestion::select(\DB::raw("{$this->ql}question AS question, answer"))
                        ->where('product_booking_id', $booking->id)
                        ->where('type', 1)
                        ->get();

                    $answered_questions['by_vendor'] = \App\Models\ProductBookingQuestion::select(\DB::raw("{$this->ql}question AS question, answer"))
                        ->where('product_booking_id', $booking->id)
                        ->where('type', 2)
                        ->get();
                }
                $booking->answered_questions = $answered_questions;
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
            'id' => "required|numeric|exists:product_bookings,id,vendor_id,{$vendor->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $booking = \App\Models\ProductBooking::find($dataArr->id);
            if ($booking->status > 1) {
                return errorMessage('action_not_allowed');
            }

            // Start Transaction
            \DB::beginTransaction();

            $booking->status = 6;
            $booking->save();

            // Trying to send Notification
            \App\Jobs\Notifications\Product\User\BookingAcceptedJob::dispatch(compact('booking'));

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
            'id' => "required|numeric|exists:product_bookings,id,vendor_id,{$vendor->id}",
            'reason' => 'nullable|max:2000',
        ]);
        $dataArr = arrayFromPost(['id', 'reason']);

        try {
            $booking = \App\Models\ProductBooking::find($dataArr->id);
            if ($booking->status == 4) {
                return errorMessage('booking_already_cancelled');
            } elseif (!in_array($booking->status, [1, 6])) {
                return errorMessage('action_not_allowed');
            }

            // Start Transaction
            \DB::beginTransaction();

            $booking->status = 4;
            $booking->cancellation_reason = $dataArr->reason;
            $booking->en_cancellation_reason = $dataArr->reason;
            $booking->cancellation_refund_amount = $booking->paid_amount;
            $booking->refund_date = $booking->cancellation_refund_amount > 0 ? date('Y-m-d', strtotime('+2 day')) : null;
            $booking->save();

            \App\Models\Product::where('id', $booking->product_id)->update(['booking_end_date' => null]);

            // Trying to send Notification
            \App\Jobs\Notifications\Product\User\BookingRejectedJob::dispatch(compact('booking'));

            // Trying to send email
            $userEmail = \App\Models\User::where('id', $booking->user_id)->value('email');
            \App\Jobs\Emails\User\Product\BookingRejectedJob::dispatch([
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

    public function postUploadImage(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'image' => 'required|' . config('cms.allowed_images'),
        ]);

        try {
            $image = uploadFile('image');
            return apiResponse('success', compact('image'));
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    // 1st Step
    public function postHandoverProduct(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:product_bookings,id,vendor_id,{$vendor->id}",
            'handover_time' => 'required|date_format:Y-m-d H:i|before_or_equal:now',
            'first_image' => 'required|max:250',
            'second_image' => 'required|max:250',
            'third_image' => 'required|max:250',
            'forth_image' => 'required|max:250',
            'notes' => 'nullable|max:2000',
            'otp' => 'required|digits:4',
        ]);
        $dataArr = arrayFromPost(['id', 'handover_time', 'first_image', 'second_image', 'third_image', 'forth_image', 'notes', 'otp']);

        try {
            $booking = \App\Models\ProductBooking::find($dataArr->id);
            if ($booking->status != 6) {
                return errorMessage('action_not_allowed');
            }
            if ($booking->is_vendor_handed_over_product) {
                return errorMessage('vendor_already_handover_product');
            } elseif ($booking->otp != $dataArr->otp) {
                return errorMessage('invalid_booking_otp');
            }

            // Start Transaction
            \DB::beginTransaction();

            $booking->otp = null;
            $booking->is_vendor_handed_over_product = 1;
            $booking->save();

            $bookingAction = new \App\Models\ProductBookingAction();
            $bookingAction->product_booking_id = $booking->id;
            $bookingAction->action_by = 2;
            $bookingAction->action_datetime = date('Y-m-d H:i:s', strtotime($dataArr->handover_time));
            $bookingAction->first_image = $dataArr->first_image;
            $bookingAction->second_image = $dataArr->second_image;
            $bookingAction->third_image = $dataArr->third_image;
            $bookingAction->forth_image = $dataArr->forth_image;
            $bookingAction->notes = $dataArr->notes;
            $bookingAction->save();

            // Trying to send Notification
            // \App\Jobs\Notifications\Product\User\ProductHandoverToUserJob::dispatch(compact('booking', 'bookingAction'));

            // Commit Transaction
            \DB::commit();

            return apiResponse();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function getReceiveProduct(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:product_bookings,id,vendor_id,{$vendor->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $result = \App\Models\ProductBookingAction::where('product_booking_id', $dataArr->id)
                ->where('action_by', 1)
                ->first();
            if (blank($result)) {
                return errorMessage('action_not_allowed');
            }

            $result->questions = \App\Models\BookingQuestion::select(\DB::raw("id, {$this->ql}question AS question"))
                ->where('type', 2)
                ->orderBy('display_order')
                ->get();

            return apiResponse('success', $result);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    // 4th Step
    public function postReceiveProduct(Request $request)
    {
        $vendor = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:product_bookings,id,vendor_id,{$vendor->id}",
            'questions' => 'required|array',
            'questions.*.id' => 'required|distinct|numeric|exists:booking_questions,id,type,2',
            'questions.*.answer' => 'required|in:0,1',
        ]);
        $dataArr = arrayFromPost(['id', 'questions']);

        try {
            $booking = \App\Models\ProductBooking::find($request->id);
            if (!$booking->is_user_handed_over_product) {
                return errorMessage('user_havnt_handover_product');
            } elseif ($booking->is_vendor_received_product) {
                return errorMessage('vendor_already_receive_product');
            }
            // Start Transaction
            \DB::beginTransaction();

            // Calculate Booking Charges Again
            $bookingDetails = \App\Models\ProductBookingDetail::where('product_booking_id', $booking->id)->first();
            $fromDate = \App\Models\ProductBookingAction::where('product_booking_id', $booking->id)->where('action_by', 2)->value('action_datetime');
            $toDate = \App\Models\ProductBookingAction::where('product_booking_id', $booking->id)->where('action_by', 1)->value('action_datetime');

            // Calculate Delay Charges
            $extra_hours = $extra_hours_charges = 0;
            if (strtotime($toDate) > strtotime($booking->to_date)) {
                $extraHoursData = calculateDelayCharges($fromDate, $toDate, $bookingDetails->delay_charges, $bookingDetails->delay_charges_type);
                if ($extraHoursData['total_hours'] > $booking->total_hours) {
                    $extra_hours = $extraHoursData['total_hours'];
                    $extra_hours_charges = $extraHoursData['subtotal'];
                }
            }

            // $calculations = bookingCalculation($fromDate, $toDate, $bookingDetails->amount, $bookingDetails->amount_type, $booking->drop_charges, 0, $booking->tax_percentage);
            // $calculations = bookingCalculation($fromDate, $toDate, $bookingDetails->amount, $bookingDetails->amount_type, ($booking->drop_charges + $extra_hours_charges), $booking->security_amount, $booking->tax_percentage);
            // $new_total_hours = $calculations['days'] * 24 + $calculations['hours'];

            // $admin_amount = ($calculations['total_amount'] + $booking->security_amount) * $booking->admin_commission_percent * 0.01;
            // $vendor_amount = ($calculations['total_amount'] + $booking->security_amount) - $admin_amount;

            // Update Booking
            // $booking->actual_from_date = $booking->from_date;
            // $booking->actual_to_date = $booking->to_date;
            // $booking->from_date = date('Y-m-d H:i:s', strtotime($fromDate));
            // $booking->to_date = date('Y-m-d H:i:s', strtotime($toDate));
            // $booking->no_of_days = $calculations['days'];
            // $booking->no_of_hours = $calculations['hours'];
            // $booking->total_hours = $new_total_hours;
            // $booking->subtotal = $calculations['subtotal'];
            // $booking->tax_amount = $calculations['tax_amount'];
            // $booking->total_amount = $calculations['total_amount'];
            // $booking->actual_amount = $calculations['total_amount'];
            $booking->extra_hours = $extra_hours;
            $booking->extra_hours_charges = $extra_hours_charges;
            // $booking->admin_amount = $admin_amount;
            // $booking->vendor_amount = $vendor_amount;
            $booking->is_vendor_received_product = 1;
            $booking->dispute_end_date = date('Y-m-d', strtotime("+{$booking->dispute_days} DAY"));
            // $booking->refundable_amount = $booking->paid_amount - $booking->actual_amount;
            $booking->refundable_amount = 0;
            $booking->refund_date = $booking->refundable_amount > 0 ? date('Y-m-d', strtotime('+2 day')) : null;
            $booking->status = 3;
            $booking->save();

            $bookingActionId = \App\Models\ProductBookingAction::where('product_booking_id', $booking->id)->where('action_by', 1)->value('id');

            foreach ($dataArr->questions as $row) {
                $bookingQuestion = \App\Models\BookingQuestion::find($row['id']);

                $bookingQuestionAns = new \App\Models\ProductBookingQuestion();
                $bookingQuestionAns->product_booking_id = $booking->id;
                $bookingQuestionAns->product_booking_action_id = $bookingActionId;
                $bookingQuestionAns->type = 2;
                $bookingQuestionAns->question = $bookingQuestion->question;
                $bookingQuestionAns->en_question = $bookingQuestion->en_question;
                $bookingQuestionAns->answer = $row['answer'];
                $bookingQuestionAns->save();
            }

            // Trying to send Notification
            \App\Jobs\Notifications\Product\User\ProductReceivedByVendorJob::dispatch(compact('booking'));

            // Trying to send email
            $userEmail = \App\Models\User::where('id', $booking->user_id)->value('email');

            \App\Jobs\Emails\User\Product\BookingCompletedJob::dispatch([
                'email' => $userEmail,
                'locale' => $this->locale,
                'booking' => $booking,
                'booking_details' => $bookingDetails,
            ]);

            // Trying to send email
            \App\Jobs\Emails\Vendor\Product\BookingProductReturnedJob::dispatch([
                'locale' => $this->locale,
                'booking' => $booking,
            ]);

            // Increment Product Booking
            $product = \App\Models\Product::find($booking->product_id);
            if (!blank($product)) {
                $product->total_bookings++;
                $product->booking_end_date = null;
                $product->save();
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
}
