<?php

namespace App\Http\Controllers\API\User\V1;

use App\Http\Controllers\API\User\UserController;
use Illuminate\Http\Request;

class ServiceBookingController extends UserController
{
    public function postBookingCalculation(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'from_datetime' => 'required|date_format:Y-m-d H:i|before:to_datetime|after_or_equal:' . date('Y-m-d H:i', strtotime('-15 minutes')),
            'to_datetime' => 'required|date_format:Y-m-d H:i|after:from_datetime',
            'service_id' => 'required|numeric|exists:services,id',
            'service_type' => 'required|numeric|in:0,1', // Provide Server at Customer Location, 0. No, 1.Yes
        ]);
        $dataArr = arrayFromPost(['from_datetime', 'to_datetime', 'service_id', 'service_type']);

        try {
            $service = \App\Models\Service::find($dataArr->service_id);
            $visiting_charges = $dataArr->service_type == 1 ? $service->visiting_charges : 0;
            $response = bookingCalculation($dataArr->from_datetime, $dataArr->to_datetime, $service->amount, $service->amount_type, $visiting_charges);
            return apiResponse('success', $response);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postBook(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'service_id' => 'required|numeric|exists:services,id',
            'from_datetime' => 'required|date_format:Y-m-d H:i|before:to_datetime|after_or_equal:' . date('Y-m-d H:i', strtotime('-15 minutes')),
            'to_datetime' => 'required|date_format:Y-m-d H:i|after:from_datetime',
            'no_of_days' => 'required|numeric|' . (empty($request->no_of_days) && !empty($request->no_of_hours) ? 'min:0' : 'min:1'),
            'no_of_hours' => 'required|numeric|max:23.99|' . (empty($request->no_of_days) ? 'min:0.01' : 'min:0'),
            'subtotal' => 'required|numeric|gt:0',
            'tax_amount' => 'required|numeric|min:0',
            'payable_amount' => 'required|numeric|gt:0',
            'service_type' => 'required|numeric|in:0,1', // Provide Server at Customer Location, 0. No, 1.Yes
            'visiting_charges' => $request->service_type == 1 ? 'required|numeric|gte:0' : 'nullable|numeric|gte:0',
            'location' => $request->service_type == 1 ? 'required' : 'nullable',
            'latitude' => $request->service_type == 1 ? 'required|numeric' : 'nullable|numeric',
            'longitude' => $request->service_type == 1 ? 'required|numeric' : 'nullable|numeric',
        ]);
        $dataArr = arrayFromPost(['service_id', 'from_datetime', 'to_datetime', 'no_of_days', 'no_of_hours', 'subtotal', 'service_type', 'tax_amount', 'payable_amount', 'visiting_charges', 'location', 'latitude', 'longitude']);

        try {
            $service = \App\Models\Service::find($dataArr->service_id);

            // Check Service availability
            $result = \App\Models\ServiceBooking::where(function ($query) use ($dataArr) {
                $query->whereBetween('from_date', [date('Y-m-d H:i:s', strtotime($dataArr->from_datetime)), date('Y-m-d H:i:s', strtotime($dataArr->to_datetime))])
                    ->orWhereBetween('to_date', [date('Y-m-d H:i:s', strtotime($dataArr->from_datetime)), date('Y-m-d H:i:s', strtotime($dataArr->to_datetime))]);
            })
                ->where('service_id', $dataArr->service_id)
                ->whereIn('status', [1, 2])
                ->where('payment_status', 2)
                ->exists();
            if ($result) {
                return errorMessage('cant_book_service');
            }

            $visiting_charges = $dataArr->service_type == 1 ? $service->visiting_charges : 0;
            $calculations = bookingCalculation($dataArr->from_datetime, $dataArr->to_datetime, $service->amount, $service->amount_type, $visiting_charges);

            $total_hours = $calculations['days'] * 24 + $calculations['hours'];
            if ($dataArr->no_of_days != $calculations['days'] || $dataArr->no_of_hours != $calculations['hours']) {
                return errorMessage('invalid_btw_days');
            }

            if (!compareNumbers($calculations['subtotal'], $dataArr->subtotal)) {
                return errorMessage('invalid_subtotal');
            }

            // Calculating Tax
            $tax_percentage = getAppSetting('tax_percentage');
            if (!compareNumbers($calculations['tax_amount'], $dataArr->tax_amount)) {
                return errorMessage('invalid_tax_amount');
            }

            if ($visiting_charges != $dataArr->visiting_charges) {
                return errorMessage('invalid_visiting_charges');
            }

            if (!compareNumbers($calculations['total_amount'], $dataArr->payable_amount)) {
                return errorMessage('invalid_total_amount');
            }

            // Start Transaction
            \DB::beginTransaction();

            $admin_commission_percent = getAppSetting('admin_service_booking_commission');
            $admin_amount = $calculations['total_amount'] * $admin_commission_percent * 0.01;
            $vendor_amount = $calculations['total_amount'] - $admin_amount;

            $newBooking = new \App\Models\ServiceBooking();
            $newBooking->user_id = $user->id;
            $newBooking->vendor_id = $service->vendor_id;
            $newBooking->service_id = $service->id;
            $newBooking->booking_code = generateBookingCode();
            $newBooking->from_date = date('Y-m-d H:i:s', strtotime($dataArr->from_datetime));
            $newBooking->to_date = date('Y-m-d H:i:s', strtotime($dataArr->to_datetime));
            $newBooking->no_of_days = $calculations['days'];
            $newBooking->no_of_hours = $calculations['hours'];
            $newBooking->total_hours = $total_hours;
            $newBooking->subtotal = $calculations['subtotal'];
            $newBooking->tax_percentage = $tax_percentage;
            $newBooking->tax_amount = $calculations['tax_amount'];
            $newBooking->total_amount = $calculations['total_amount'];
            $newBooking->paid_amount = $newBooking->total_amount;
            $newBooking->visiting_charges = $visiting_charges;
            $newBooking->admin_amount = $admin_amount;
            $newBooking->vendor_amount = $vendor_amount;
            $newBooking->dispute_days = getAppSetting('raise_dispute_days');
            $newBooking->admin_commission_percent = $admin_commission_percent;
            $newBooking->user_otp = generateOtp();
            $newBooking->save();

            $category = \App\Models\Category::find($service->category_id);
            $subCategory = \App\Models\Category::find($service->sub_category_id);

            $cities = $enCities = '';
            $serviceCities = \App\Models\City::select(\DB::raw('cities.name, cities.en_name'))
                ->join('service_cities', 'service_cities.city_id', '=', 'cities.id')
                ->where('service_cities.service_id', $service->id)
                ->get();
            if ($serviceCities->isNotEmpty()) {
                $cities = $serviceCities->pluck('name')->implode(', ');
                $enCities = $serviceCities->pluck('en_name')->implode(', ');
            }
            
            // Add Service Details
            $bookingDetail = new \App\Models\ServiceBookingDetail();
            $bookingDetail->service_booking_id = $newBooking->id;
            $bookingDetail->vendor_id = $newBooking->vendor_id;
            $bookingDetail->image = $service->default_image;
            $bookingDetail->name = $service->name;
            $bookingDetail->en_name = $service->en_name;
            $bookingDetail->description = $service->description;
            $bookingDetail->en_description = $service->en_description;
            $bookingDetail->amount = $service->amount;
            $bookingDetail->amount_type = $service->amount_type;
            $bookingDetail->service_type = $dataArr->service_type;
            $bookingDetail->user_address = $dataArr->location;
            $bookingDetail->user_latitude = $dataArr->latitude;
            $bookingDetail->user_longitude = $dataArr->longitude;
            $bookingDetail->address = $service->address;
            $bookingDetail->latitude = $service->latitude;
            $bookingDetail->longitude = $service->longitude;
            $bookingDetail->category_id = @$category->id;
            $bookingDetail->category_name = @$category->name;
            $bookingDetail->en_category_name = @$category->en_name;
            $bookingDetail->sub_category_id = @$subCategory->id;
            $bookingDetail->sub_category_name = @$subCategory->name;
            $bookingDetail->en_sub_category_name = @$subCategory->en_name;
            $bookingDetail->cities = $cities;
            $bookingDetail->en_cities = $enCities;
            $bookingDetail->save();

            // Trying to send email
            \App\Jobs\Emails\User\Service\NewBookingJob::dispatch([
                'email' => $user->email,
                'locale' => $this->locale,
                'booking' => $newBooking,
                'booking_details' => $bookingDetail,
            ]);

            // Trying to send email
            \App\Jobs\Emails\Vendor\Service\NewBookingJob::dispatch([
                'booking' => $newBooking,
                'booking_details' => $bookingDetail,
                'locale' => $this->locale,
            ]);


            // Commit Transaction
            \DB::commit();

            return apiResponse('success', ['id' => $newBooking->id, 'booking_code' => $newBooking->booking_code]);
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function generatePaymentUrl(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:service_bookings,id,user_id,{$user->id}",
            'payment_type' => 'required|in:1,2' //1.MADA, 2.VISA/MASTER
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $booking = \App\Models\ServiceBooking::find($dataArr->id);
            if (blank($booking) && $booking->payment_status == 2) {
                return errorMessage('invalid_request');
            }

            // Start Transaction
            \DB::beginTransaction();

            // Add Payment History
            $paymentHistory = new \App\Models\BookingPaymentHistory();
            $paymentHistory->booking_type = 2;
            $paymentHistory->service_booking_id = $dataArr->id;
            $paymentHistory->user_id = $user->id;
            $paymentHistory->payment_date = date('Y-m-d H:i:s');
            $paymentHistory->mode_of_payment = 2;
            $paymentHistory->transaction_id = generateRandomString(15, 'lower_case,numbers');
            $paymentHistory->amount = $booking->total_amount;
            $paymentHistory->save();

            $response = \App\Helpers\NoonPayment::getInstance()->initiate([
                'amount' => number_format($booking->total_amount, 2, '.', ''),
                'item_name' => $booking->booking_code,
                'reference_id' => $paymentHistory->transaction_id,
                'locale' => $this->locale,
            ]);

            if ($response->err) {
                // Rollback Transaction
                \DB::rollBack();
                return errorMessage($response->message, true);
            }

            $booking->payment_init_id = @$response->data->result->order->id;
            $booking->save();

            // Commit Transaction
            \DB::commit();

            return apiResponse('success', ['redirect_url' => $response->data->result->checkoutData->postUrl]);
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return errorMessage($e->getMessage(), true);
        }
    }

    public function getListing(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'page' => 'required|min:1',
        ]);

        try {
            $list = \App\Models\ServiceBooking::select(\DB::raw("service_bookings.id, service_bookings.booking_code, service_bookings.payment_status, service_bookings.from_date, service_bookings.to_date, service_bookings.no_of_days, service_bookings.no_of_hours, service_bookings.total_amount, service_bookings.status, service_bookings.created_at, service_bookings.service_review_id, service_reviews.rating, service_booking_details.image, service_booking_details.{$this->ql}name AS service, service_booking_details.{$this->ql}category_name AS category, service_booking_details.{$this->ql}sub_category_name AS sub_category, service_booking_details.amount, service_booking_details.amount_type, CONCAT(vendors.dial_code, vendors.mobile) AS vendor_mobile_no"))
                ->join('service_booking_details', 'service_booking_details.service_booking_id', '=', 'service_bookings.id')
                ->leftJoin('service_reviews', 'service_reviews.id', '=', 'service_bookings.service_review_id')
                ->leftJoin('vendors', 'vendors.id', '=', 'service_bookings.vendor_id')
                ->where('service_bookings.user_id', $user->id)
                ->orderBy('service_bookings.id', 'desc')
                ->paginate(10);

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function getDetails(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:service_bookings,id,user_id,{$user->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $booking = \App\Models\ServiceBooking::select(\DB::raw("id, booking_code, from_date, to_date, no_of_days, no_of_hours, subtotal, visiting_charges, tax_percentage, tax_amount, total_amount, is_cancelled_amount_refunded, cancellation_refund_amount, cancellation_refund_at, payment_status, status, vendor_id, service_review_id, {$this->ql}cancellation_reason AS cancellation_reason, dispute_end_date, user_otp, created_at"))
                ->find($dataArr->id);
            if (!blank($booking)) {
                $booking->service = \App\Models\ServiceBookingDetail::select(\DB::raw("id, image, name, en_name, service_type, address, latitude, longitude, user_address, user_latitude, user_longitude, {$this->ql}category_name AS category, {$this->ql}sub_category_name AS sub_category, amount, amount_type, {$this->ql}cities AS cities"))
                    ->where('service_booking_id', $booking->id)
                    ->first();

                $booking->vendor = null;
                if ($booking->payment_status == 2 && in_array($booking->status, [2, 3, 6])) {
                    $booking->vendor = \App\Models\Vendor::select(\DB::raw('name, dial_code, mobile'))
                        ->where('id', $booking->vendor_id)
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

    public function getCancellationReasonsList(Request $request)
    {
        $user = getTokenUser();

        try {
            $list = \App\Models\BookingCancellationQuestion::select(\DB::raw("id, {$this->ql}question AS question"))
                ->where('type', 2)
                ->where('status', 1)
                ->orderBy("{$this->ql}question")
                ->get();

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    // 1.Pending, 2.Ongoing, 3.Completed, 4.Rejected, 5.Cancelled
    public function postCancel(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:service_bookings,id,user_id,{$user->id}",
            'other_reason' => 'required_without:cancel_reason_id|max:2000',
            'cancel_reason_id' => 'required_without:other_reason|exists:booking_cancellation_questions,id,type,2',
        ]);
        $dataArr = arrayFromPost(['id', 'other_reason', 'cancel_reason_id']);

        try {
            $booking = \App\Models\ServiceBooking::find($dataArr->id);
            if ($booking->status == 5) {
                return errorMessage('booking_already_cancelled');
            } elseif ($booking->status > 1) {
                return errorMessage('action_not_allowed');
            }

            // Start Transaction
            \DB::beginTransaction();


            if (!blank($dataArr->other_reason)) {
                $booking->cancellation_reason = $dataArr->other_reason;
                $booking->en_cancellation_reason = $dataArr->other_reason;
            } else {
                $reason = \App\Models\BookingCancellationQuestion::find($dataArr->cancel_reason_id);

                $booking->cancellation_reason = $reason->question;
                $booking->en_cancellation_reason = $reason->en_question;
            }

            if ($booking->payment_status == 2) {
                $booking->cancellation_charges = getAppSetting('service_cancellation_charges');
                $booking->cancellation_refund_amount = $booking->paid_amount - $booking->cancellation_charges;
                $booking->refund_date = $booking->cancellation_refund_amount > 0 ? date('Y-m-d', strtotime('+2 day')) : null;
            }
            $booking->status = 5;
            $booking->save();

            // Trying to send Notification
            \App\Jobs\Notifications\Service\Vendor\BookingCancelledJob::dispatch(compact('booking'));

            // Trying to send email
            \App\Jobs\Emails\Vendor\Service\BookingCancelledJob::dispatch([
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

    public function postComplete(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:service_bookings,id,user_id,{$user->id}",
            'otp' => 'required|digits:4',
        ]);
        $dataArr = arrayFromPost(['id', 'otp']);

        try {
            $booking = \App\Models\ServiceBooking::find($dataArr->id);
            if ($booking->status == 3) {
                return errorMessage('booking_already_completed');
            } elseif ($booking->status != 2) {
                return errorMessage('action_not_allowed');
            } elseif ($booking->vendor_otp != $dataArr->otp) {
                return errorMessage('invalid_booking_otp');
            }

            // Start Transaction
            \DB::beginTransaction();

            // Calculate Booking Charges Again
            // $bookingDetails = \App\Models\ServiceBookingDetail::select(\DB::raw('amount, amount_type'))
            //     ->where('service_booking_id', $booking->id)
            //     ->first();

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
            $booking->completed_by = 1;
            $booking->status = 3;
            $booking->save();

            // Commit Transaction
            \DB::commit();

            // Trying to send Notification
            \App\Jobs\Notifications\Service\Vendor\BookingCompletedJob::dispatch(compact('booking'));

            // Trying to send email
            \App\Jobs\Emails\Vendor\Service\BookingCompletedJob::dispatch([
                'locale' => $this->locale,
                'booking' => $booking,
            ]);

            return apiResponse('success');
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }

    public function postSubmitRating(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:service_bookings,id,user_id,{$user->id}",
            'rating' => 'required|numeric|in:1,2,3,4,5',
            'comments' => 'nullable|max:1500',
        ]);
        $dataArr = arrayFromPost(['id', 'rating', 'comments']);

        try {
            $booking = \App\Models\ServiceBooking::find($dataArr->id);
            if ($booking->status < 3) {
                return errorMessage('booking_not_completed_for_rating');
            } elseif ($booking->status != 3) {
                return errorMessage('action_not_allowed');
            } elseif (!blank($booking->service_review_id)) {
                return errorMessage('service_already_reviewed');
            }

            // Start Transaction
            \DB::beginTransaction();

            $serviceReview = new \App\Models\ServiceReview();
            $serviceReview->user_id = $user->id;
            $serviceReview->service_id = $booking->service_id;
            $serviceReview->service_booking_id = $booking->id;
            $serviceReview->rating = $dataArr->rating;
            $serviceReview->comments = $dataArr->comments;
            $serviceReview->save();

            // Update Booking
            $booking->service_review_id = $serviceReview->id;
            $booking->save();

            $response = calculateRating($serviceReview->service_id, 'service');
            $service = \App\Models\Service::find($serviceReview->service_id);
            if (!blank($service)) {
                $service->avg_rating = $response->rating;
                $service->total_ratings = $response->voters;
                $service->save();
            }
            // Commit Transaction
            \DB::commit();

            return apiResponse();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            return exceptionErrorMessage($e);
        }
    }
}
