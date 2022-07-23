<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function getInit(Request $request)
    {
        $response = \App\Helpers\NoonPayment::getInstance()->initiate([
            'amount' => number_format(1, 2, '.', ''),
            'item_name' => 'Test Product',
            'reference_id' => generateRandomString(20),
            'locale' => 'en',
            'returnUrl' => route('test.payment.response'),
        ]);

        if ($response->err) {
            dd($response);
        }

        return redirect($response->data->result->checkoutData->postUrl);
    }

    public function getTestResp(Request $request)
    {
        $response = \App\Helpers\NoonPayment::getInstance()->getResponse($request->orderId);
        dd($response);
    }

    public function getResponse(Request $request)
    {
        try {
            \Log::channel('payment_logs')->info('Payment Callback Data');
            \Log::channel('payment_logs')->info(json_encode($request->all()));

            $paymentHistory = \App\Models\BookingPaymentHistory::where('transaction_id', @$request->merchantReference)->first();
            if (blank($paymentHistory)) {
                return redirect()->route('payment.failed');
            }

            $booking = $paymentHistory->product_booking_id ?
                \App\Models\ProductBooking::find($paymentHistory->product_booking_id) :
                \App\Models\ServiceBooking::find($paymentHistory->service_booking_id);

            // Start Transaction
            \DB::beginTransaction();

            $response = \App\Helpers\NoonPayment::getInstance()->getResponse($request->orderId);
            if ($response->err) {
                $paymentHistory->status = 3;
                $paymentHistory->gateway_response = serialize($response->data);
                $paymentHistory->save();

                // Update Booking Payment Status
                $booking->payment_status = 3;
                $booking->save();

                // Commit Transaction
                \DB::commit();

                return redirect()->route('payment.failed', ['msg' => urlencode($response->message)]);
            }

            // Update Payment History
            $paymentHistory->status = 2;
            $paymentHistory->gateway_response = serialize($response->data);
            $paymentHistory->save();

            $booking->payment_status = 2;
            $booking->transaction_id = @$response->result->transactions[0]->id;
            $booking->save();

            // Update product
            if ($paymentHistory->product_booking_id) {
                $product = \App\Models\Product::find($booking->product_id);
                if (!blank($product)) {
                    $product->booking_end_date = $booking->to_date;
                    $product->save();
                }
            }

            // Commit Transaction
            \DB::commit();

            return redirect()->route('payment.success', ['code' => $booking->booking_code]);
        } catch (\Throwable $th) {
            // Rollback Transaction
            \DB::rollBack();

            \Log::channel('payment_logs')->info('Payment Error');
            \Log::channel('payment_logs')->info($th);

            return redirect()->route('payment.failed');
        }
    }

    public function getSuccess(Request $request)
    {
        echo 'Payment Success';
    }

    public function getFailed(Request $request)
    {
        echo 'Payment Failed';
    }

    public function getSubscriptionResponse(Request $request)
    {
        try {
            \Log::channel('payment_logs')->info('Subscription Payment Callback Data');
            \Log::channel('payment_logs')->info(json_encode($request->all()));

            $paymentHistory = \App\Models\VendorSubscriptionHistory::where('transaction_id', @$request->merchantReference)->first();
            if (blank($paymentHistory)) {
                return redirect()->route('payment.failed');
            }

            // Start Transaction
            \DB::beginTransaction();

            $response = \App\Helpers\NoonPayment::getInstance()->getResponse($request->orderId);
            if ($response->err) {
                $paymentHistory->status = 3;
                $paymentHistory->gateway_response = serialize($response->data);
                $paymentHistory->save();

                // Commit Transaction
                \DB::commit();

                return redirect()->route('payment.failed', ['msg' => urlencode($response->message)]);
            }

            // Update Payment History
            $paymentHistory->status = 2;
            $paymentHistory->gateway_response = serialize($response->data);
            $paymentHistory->save();

            $vendor = \App\Models\Vendor::find($paymentHistory->vendor_id);
            $vendor->total_sponsor_items += $paymentHistory->total_items;
            $vendor->save();

            // Commit Transaction
            \DB::commit();

            return redirect()->route('payment.success');
        } catch (\Throwable $th) {
            // Rollback Transaction
            \DB::rollBack();

            \Log::channel('payment_logs')->info('Subscription Payment Error');
            \Log::channel('payment_logs')->info($th);

            return redirect()->route('payment.failed');
        }
    }
}
