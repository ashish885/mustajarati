<?php

namespace App\Http\Controllers\API\User\V1;

use App\Http\Controllers\API\User\UserController;
use Illuminate\Http\Request;

class ProductBookingController extends UserController
{
    public function postBookingCalculation(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'product_id' => 'required|numeric|exists:products,id',
            'from_datetime' => 'required|date_format:Y-m-d H:i|before:to_datetime|after_or_equal:' . date('Y-m-d H:i', strtotime('-15 minutes')),
            'to_datetime' => 'required|date_format:Y-m-d H:i|after:from_datetime',
            'pickup_type' => 'required|numeric|in:1,2', // 1.Pick, 2.Drop
            'security_amount_type' => 'required|numeric|in:1,2', //1.Online, 2.Offline
        ]);
        $dataArr = arrayFromPost(['from_datetime', 'to_datetime', 'product_id', 'pickup_type', 'drop_charges', 'security_amount_type']);

        try {
            $product = \App\Models\Product::find($dataArr->product_id);
            $delivery_charges = $dataArr->pickup_type == 2 ? $product->delivery_charges : 0;
            $security_amount = $dataArr->security_amount_type == 1 ? $product->security_amount : 0;

            $response = bookingCalculation($dataArr->from_datetime, $dataArr->to_datetime, $product->amount, $product->amount_type, $delivery_charges, $security_amount);

            return apiResponse('success', $response);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    public function postBook(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'product_id' => 'required|numeric|exists:products,id',
            'from_datetime' => 'required|date_format:Y-m-d H:i|before:to_datetime|after_or_equal:' . date('Y-m-d H:i', strtotime('-15 minutes')),
            'to_datetime' => 'required|date_format:Y-m-d H:i|after:from_datetime',
            'no_of_days' => 'required|numeric|' . (empty($request->no_of_days) && !empty($request->no_of_hours) ? 'min:0' : 'min:1'),
            'no_of_hours' => 'required|numeric|max:23.99|' . (empty($request->no_of_days) ? 'min:0.01' : 'min:0'),
            'security_amount_type' => 'required|numeric|in:1,2', //1.Online, 2.Offline
            'security_amount' => 'required|numeric|gte:0',
            'subtotal' => 'required|numeric|gt:0',
            'tax_amount' => 'required|numeric|min:0',
            'payable_amount' => 'required|numeric|gt:0',
            'pickup_type' => 'required|numeric|in:1,2', // 1.Pick, 2.Drop
            'drop_charges' => $request->pickup_type == 2 ? 'required|numeric|gte:0' : 'nullable|numeric|gte:0',
            'drop_location' => $request->pickup_type == 2 ? 'required' : 'nullable',
            'drop_latitude' => $request->pickup_type == 2 ? 'required|numeric' : 'nullable|numeric',
            'drop_longitude' => $request->pickup_type == 2 ? 'required|numeric' : 'nullable|numeric',
        ]);
        $dataArr = arrayFromPost(['product_id', 'from_datetime', 'to_datetime', 'no_of_days', 'no_of_hours', 'security_amount', 'drop_charges', 'subtotal', 'pickup_type', 'tax_amount', 'payable_amount', 'drop_location', 'drop_latitude', 'drop_longitude', 'security_amount_type']);

        try {
            $product = \App\Models\Product::find($dataArr->product_id);
            if (!$product->status || !is_null($product->booking_end_date)) {
                return errorMessage('product_not_available');
            }

            // Check Product availability
            // $result = \App\Models\ProductBooking::where(function ($query) use ($dataArr) {
            //     $query->whereBetween('from_date', [date('Y-m-d H:i:s', strtotime($dataArr->from_datetime)), date('Y-m-d H:i:s', strtotime($dataArr->to_datetime))])
            //         ->orWhereBetween('to_date', [date('Y-m-d H:i:s', strtotime($dataArr->from_datetime)), date('Y-m-d H:i:s', strtotime($dataArr->to_datetime))]);
            // })
            //     ->where('product_id', $dataArr->product_id)
            //     ->whereIn('status', [2])
            //     ->exists();
            // if ($result) {
            //     return errorMessage('cant_book_product');
            // }

            if ($product->security_amount != $dataArr->security_amount) {
                return errorMessage('invalid_security_amount');
            }

            $drop_charges = $dataArr->pickup_type == 2 ? $product->delivery_charges : 0;
            $security_amount = $dataArr->security_amount_type == 1 ? $product->security_amount : 0;
            $calculations = bookingCalculation($dataArr->from_datetime, $dataArr->to_datetime, $product->amount, $product->amount_type, $drop_charges, $security_amount);

            $noOfDays = getBtwDays($dataArr->from_datetime, $dataArr->to_datetime);
            $noOfHours = getBtwDays($dataArr->from_datetime, $dataArr->to_datetime, 'hours');
            $total_hours = $noOfDays * 24 + $noOfHours;
            if ($dataArr->no_of_days != $noOfDays || $dataArr->no_of_hours != $noOfHours) {
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

            if (!compareNumbers($drop_charges, $dataArr->drop_charges)) {
                return errorMessage('invalid_drop_charges');
            }

            if (!compareNumbers($calculations['total_amount'], $dataArr->payable_amount)) {
                return errorMessage('invalid_total_amount');
            }

            // Start Transaction
            \DB::beginTransaction();

            $admin_commission_percent = getAppSetting('admin_product_booking_commission');
            $admin_amount = ($calculations['total_amount'] - $security_amount) * $admin_commission_percent * 0.01;
            $vendor_amount = ($calculations['total_amount'] - $security_amount) - $admin_amount;

            $newBooking = new \App\Models\ProductBooking();
            $newBooking->user_id = $user->id;
            $newBooking->vendor_id = $product->vendor_id;
            $newBooking->product_id = $product->id;
            $newBooking->booking_code = generateBookingCode();
            $newBooking->from_date = date('Y-m-d H:i:s', strtotime($dataArr->from_datetime));
            $newBooking->to_date = date('Y-m-d H:i:s', strtotime($dataArr->to_datetime));
            $newBooking->no_of_days = $noOfDays;
            $newBooking->no_of_hours = $noOfHours;
            $newBooking->total_hours = $total_hours;
            $newBooking->subtotal = $calculations['subtotal'];
            $newBooking->drop_charges = $drop_charges;
            $newBooking->tax_percentage = $tax_percentage;
            $newBooking->tax_amount = $calculations['tax_amount'];
            $newBooking->total_amount = $calculations['total_amount'];
            $newBooking->paid_amount = $calculations['total_amount'];
            $newBooking->security_amount_type = $dataArr->security_amount_type;
            $newBooking->security_amount = $dataArr->security_amount_type == 1 ? $product->security_amount : 0;
            $newBooking->offline_security_amount = $dataArr->security_amount_type == 2 ? $product->security_amount : 0;
            $newBooking->actual_amount = $calculations['total_amount'] - $newBooking->security_amount;
            $newBooking->admin_amount = $admin_amount;
            $newBooking->vendor_amount = $vendor_amount;
            $newBooking->dispute_days = getAppSetting('raise_dispute_days');
            $newBooking->admin_commission_percent = $admin_commission_percent;
            $newBooking->otp = generateOtp();
            $newBooking->save();

            $category = \App\Models\Category::find($product->category_id);
            $subCategory = \App\Models\Category::find($product->sub_category_id);

            $cities = $enCities = '';
            $productCities = \App\Models\City::select(\DB::raw('cities.name, cities.en_name'))
                ->join('product_cities', 'product_cities.city_id', '=', 'cities.id')
                ->where('product_cities.product_id', $product->id)
                ->get();
            if ($productCities->isNotEmpty()) {
                $cities = $productCities->pluck('name')->implode(', ');
                $enCities = $productCities->pluck('en_name')->implode(', ');
            }

            // Add Product Details
            $bookingDetail = new \App\Models\ProductBookingDetail();
            $bookingDetail->product_booking_id = $newBooking->id;
            $bookingDetail->vendor_id = $newBooking->vendor_id;
            $bookingDetail->image = $product->default_image;
            $bookingDetail->name = $product->name;
            $bookingDetail->en_name = $product->en_name;
            $bookingDetail->description = $product->description;
            $bookingDetail->en_description = $product->en_description;
            $bookingDetail->features = $product->features;
            $bookingDetail->en_features = $product->en_features;
            $bookingDetail->amount = $product->amount;
            $bookingDetail->amount_type = $product->amount_type;
            $bookingDetail->daily_amount = $product->daily_amount;
            $bookingDetail->delay_charges = $product->delay_charges;
            $bookingDetail->delay_charges_type = $product->delay_charges_type;
            $bookingDetail->pickup_type = $dataArr->pickup_type;
            $bookingDetail->drop_location = $dataArr->drop_location;
            $bookingDetail->drop_latitude = $dataArr->drop_latitude;
            $bookingDetail->drop_longitude = $dataArr->drop_longitude;
            $bookingDetail->pickup_location = $product->location;
            $bookingDetail->pickup_latitude = $product->latitude;
            $bookingDetail->pickup_longitude = $product->longitude;
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
            \App\Jobs\Emails\User\Product\NewBookingJob::dispatch([
                'email' => $user->email,
                'locale' => $this->locale,
                'booking' => $newBooking,
                'booking_details' => $bookingDetail,
            ]);

            // Trying to send email
            \App\Jobs\Emails\Vendor\Product\NewBookingJob::dispatch([
                'locale' => $this->locale,
                'booking' => $newBooking,
                'booking_details' => $bookingDetail,
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
            'id' => "required|numeric|exists:product_bookings,id,user_id,{$user->id}",
            'payment_type' => 'required|in:1,2', //1.MADA, 2.VISA/MASTER
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $booking = \App\Models\ProductBooking::find($dataArr->id);
            if (blank($booking) && $booking->payment_status == 2) {
                return errorMessage('invalid_request');
            }

            // Start Transaction
            \DB::beginTransaction();

            // Add Payment History
            $paymentHistory = new \App\Models\BookingPaymentHistory();
            $paymentHistory->booking_type = 1;
            $paymentHistory->product_booking_id = $dataArr->id;
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
            $list = \App\Models\ProductBooking::select(\DB::raw("product_bookings.id, product_bookings.booking_code, product_bookings.no_of_days, product_bookings.no_of_hours, product_bookings.from_date, product_bookings.to_date, product_bookings.total_amount, product_bookings.actual_amount, product_bookings.payment_status, product_bookings.status, product_bookings.created_at, product_bookings.product_review_id, product_reviews.rating, product_booking_details.image, product_booking_details.{$this->ql}name AS product_name, product_booking_details.{$this->ql}category_name AS category, product_booking_details.{$this->ql}sub_category_name AS sub_category, CONCAT(vendors.dial_code, vendors.mobile) AS vendor_mobile_no, product_booking_details.amount, product_booking_details.amount_type"))
                ->join('product_booking_details', 'product_booking_details.product_booking_id', '=', 'product_bookings.id')
                ->leftJoin('product_reviews', 'product_reviews.id', '=', 'product_bookings.product_review_id')
                ->leftJoin('vendors', 'vendors.id', '=', 'product_bookings.vendor_id')
                ->where('product_bookings.user_id', $user->id)
                ->orderBy('product_bookings.id', 'desc')
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
            'id' => "required|numeric|exists:product_bookings,id,user_id,{$user->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $booking = \App\Models\ProductBooking::select(\DB::raw("id, booking_code, from_date, to_date, no_of_days, no_of_hours, subtotal, security_amount_type, security_amount, offline_security_amount, drop_charges, tax_percentage, tax_amount, total_amount, paid_amount, extra_hours, extra_hours_charges, damage_charges, refundable_amount, is_user_received_product, is_user_handed_over_product, is_vendor_received_product, is_vendor_handed_over_product, is_refund_initiated, refund_date, is_user_pending_amount_paid, is_cancelled_amount_refunded, cancellation_refund_amount, cancellation_refund_at, payment_status, status, vendor_id, product_review_id, {$this->ql}cancellation_reason AS cancellation_reason, dispute_end_date, otp, created_at"))
                ->find($dataArr->id);
            if (!blank($booking)) {
                // $booking->total_extra_charges = (string) ($booking->extra_hours_charges + $booking->drop_charges + $booking->damage_charges);
                $booking->total_extra_charges = (string) ($booking->drop_charges + $booking->damage_charges);

                $booking->product = \App\Models\ProductBookingDetail::select(\DB::raw("id, image, name, en_name, pickup_type, drop_location, drop_latitude, drop_longitude, pickup_location, pickup_latitude, pickup_longitude, {$this->ql}category_name AS category, {$this->ql}sub_category_name AS sub_category, amount, amount_type, {$this->ql}cities AS cities"))
                    ->where('product_booking_id', $booking->id)
                    ->first();

                $booking->vendor = null;
                if ($booking->payment_status == 2 && in_array($booking->status, [6, 2, 3])) {
                    $booking->vendor = \App\Models\Vendor::select(\DB::raw('name, dial_code, mobile'))
                        ->where('id', $booking->vendor_id)
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

    public function getCancellationReasonsList(Request $request)
    {
        $user = getTokenUser();

        try {
            $list = \App\Models\BookingCancellationQuestion::select(\DB::raw("id, {$this->ql}question AS question"))
                ->where('type', 1)
                ->where('status', 1)
                ->orderBy("{$this->ql}question")
                ->get();

            return apiResponse('success', $list);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    // 1.Pending, 2.Ongoing, 3.Completed, 4.Rejected, 5.Cancelled, 6.Accept
    public function postCancel(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:product_bookings,id,user_id,{$user->id}",
            'other_reason' => 'required_without:cancel_reason_id|max:2000',
            'cancel_reason_id' => 'required_without:other_reason|exists:booking_cancellation_questions,id,type,1',
        ]);
        $dataArr = arrayFromPost(['id', 'other_reason', 'cancel_reason_id']);

        try {
            $booking = \App\Models\ProductBooking::find($dataArr->id);
            if ($booking->status == 5) {
                return errorMessage('booking_already_cancelled');
            } elseif (!in_array($booking->status, [1, 6])) {
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
                $booking->cancellation_charges = $booking->status == 6 ? getAppSetting('product_cancellation_charges') : 0;
                $booking->cancellation_refund_amount = $booking->paid_amount - $booking->cancellation_charges;
                $booking->refund_date = $booking->cancellation_refund_amount > 0 ? date('Y-m-d', strtotime('+2 day')) : null;
            }
            $booking->status = 5;
            $booking->save();

            \App\Models\Product::where('id', $booking->product_id)->update(['booking_end_date' => null]);

            // Trying to send Notification
            \App\Jobs\Notifications\Product\Vendor\BookingCancelledJob::dispatch(compact('booking'));

            // Trying to send email
            \App\Jobs\Emails\Vendor\Product\BookingCancelledJob::dispatch([
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

    public function getReceiveProduct(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:product_bookings,id,user_id,{$user->id}",
        ]);
        $dataArr = arrayFromPost(['id']);

        try {
            $result = \App\Models\ProductBookingAction::where('product_booking_id', $dataArr->id)
                ->where('action_by', 2)
                ->first();
            if (blank($result)) {
                return errorMessage('action_not_allowed');
            }

            $bookingDetails = \App\Models\ProductBookingDetail::where('product_booking_id', $dataArr->id)->first();

            $result->pickup_type = $bookingDetails->result;
            if ($result->pickup_type == 1) {
                $result->pickup_location = $bookingDetails->pickup_location;
                $result->pickup_latitude = $bookingDetails->pickup_latitude;
                $result->pickup_longitude = $bookingDetails->pickup_longitude;
            }

            $result->questions = \App\Models\BookingQuestion::select(\DB::raw("id, {$this->ql}question AS question"))
                ->where('type', 1)
                ->get();

            return apiResponse('success', $result);
        } catch (\Exception $e) {
            return exceptionErrorMessage($e);
        }
    }

    // 2ed Step
    public function postReceiveProduct(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:product_bookings,id,user_id,{$user->id}",
            'questions' => 'required|array',
            'questions.*.id' => 'required|distinct|numeric|exists:booking_questions,id,type,1',
            'questions.*.answer' => 'required|in:0,1',
        ]);
        $dataArr = arrayFromPost(['id', 'questions']);

        try {
            $booking = \App\Models\ProductBooking::find($request->id);
            if (!$booking->is_vendor_handed_over_product) {
                return errorMessage('vendor_havnt_handover_product');
            } elseif ($booking->is_user_received_product) {
                return errorMessage('user_already_receive_product');
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
            $booking->is_user_received_product = 1;
            $booking->status = 2;
            $booking->otp = generateOtp();
            $booking->save();

            $bookingActionId = \App\Models\ProductBookingAction::where('product_booking_id', $booking->id)->where('action_by', 2)->value('id');

            foreach ($dataArr->questions as $row) {
                $bookingQuestion = \App\Models\BookingQuestion::find($row['id']);

                $bookingQuestionAns = new \App\Models\ProductBookingQuestion();
                $bookingQuestionAns->product_booking_id = $booking->id;
                $bookingQuestionAns->product_booking_action_id = $bookingActionId;
                $bookingQuestionAns->type = 1;
                $bookingQuestionAns->question = $bookingQuestion->question;
                $bookingQuestionAns->en_question = $bookingQuestion->en_question;
                $bookingQuestionAns->answer = $row['answer'];
                $bookingQuestionAns->save();
            }

            // Trying to send Notification
            \App\Jobs\Notifications\Product\Vendor\ProductReceivedByUserJob::dispatch(compact('booking'));

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

    // 3rd Step
    public function postReturnProduct(Request $request)
    {
        $user = getTokenUser();
        $this->validate($request, [
            'id' => "required|numeric|exists:product_bookings,id,user_id,{$user->id}",
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
            if ($booking->is_user_handed_over_product) {
                return errorMessage('user_already_handover_product');
            } elseif ($booking->otp != $dataArr->otp) {
                return errorMessage('invalid_booking_otp');
            }

            // Start Transaction
            \DB::beginTransaction();

            $booking->otp = null;
            $booking->is_user_handed_over_product = 1;
            $booking->save();

            $bookingAction = new \App\Models\ProductBookingAction();
            $bookingAction->product_booking_id = $booking->id;
            $bookingAction->action_by = 1;
            $bookingAction->action_datetime = date('Y-m-d H:i:s', strtotime($dataArr->handover_time));
            $bookingAction->first_image = $dataArr->first_image;
            $bookingAction->second_image = $dataArr->second_image;
            $bookingAction->third_image = $dataArr->third_image;
            $bookingAction->forth_image = $dataArr->forth_image;
            $bookingAction->notes = $dataArr->notes;
            $bookingAction->save();

            // Trying to send Notification
            \App\Jobs\Notifications\Product\Vendor\ProductHandoverToVendorJob::dispatch(compact('booking', 'bookingAction'));

            // Commit Transaction
            \DB::commit();

            return apiResponse();
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
            'id' => "required|numeric|exists:product_bookings,id,user_id,{$user->id}",
            'rating' => 'required|numeric|in:1,2,3,4,5',
            'comments' => 'nullable|max:1500',
        ]);
        $dataArr = arrayFromPost(['id', 'rating', 'comments']);

        try {
            $booking = \App\Models\ProductBooking::find($dataArr->id);
            if ($booking->status < 3) {
                return errorMessage('booking_not_completed_for_rating');
            } elseif ($booking->status != 3) {
                return errorMessage('action_not_allowed');
            } elseif (!blank($booking->product_review_id)) {
                return errorMessage('product_already_reviewed');
            }

            // Start Transaction
            \DB::beginTransaction();

            $productReview = new \App\Models\ProductReview();
            $productReview->product_id = $booking->product_id;
            $productReview->product_booking_id = $booking->id;
            $productReview->user_id = $user->id;
            $productReview->rating = $dataArr->rating;
            $productReview->comments = $dataArr->comments;
            $productReview->save();

            // Update Booking
            $booking->product_review_id = $productReview->id;
            $booking->save();

            $response = calculateRating($productReview->product_id, 'product');
            $product = \App\Models\Product::find($productReview->product_id);
            if (!blank($product)) {
                $product->avg_rating = $response->rating;
                $product->total_ratings = $response->voters;
                $product->save();
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
