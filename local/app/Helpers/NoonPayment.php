<?php

namespace App\Helpers;

class NoonPayment
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new NoonPayment();
        }
        return self::$instance;
    }

    public function getHeaders()
    {
        return [
            "Content-type: application/json",
            "Authorization: " . env('NOON_PAYMENT_AUTH_KEY') . " " . env('NOON_PAYMENT_TOKEN_IDENTIFIER'),
        ];
    }

    public function initiate($paymentData)
    {
        $api_url = env('NOON_PAYMENT_PAYMENT_API') . 'order';
        $headers = $this->getHeaders();
        $dataArr = [
            'apiOperation' => 'INITIATE',
            'order' => [
                'amount' => $paymentData['amount'],
                'currency' => env('NOON_PAYMENT_CURRENCY'),
                'category' => env('NOON_PAYMENT_ORDER_CATEGORY'),
                'channel' => env('NOON_PAYMENT_CHANNEL'),
                'name' => $paymentData['item_name'],
                'reference' => $paymentData['reference_id'],
            ],
            'configuration' => [
                // 'generateShortLink' => true,
                'requiredCardHolderName' => false,
                'tokenizeCc' => true,
                'locale' => $paymentData['locale'],
                // 'styleProfile' => 'Demo',
                'paymentAction' => 'SALE',
                // 'paymentAction' => 'AUTHORIZE',
                'returnUrl' => isset($paymentData['returnUrl']) ? $paymentData['returnUrl'] : route('payment.response'),
            ],
        ];
        // dd(compact('api_url', 'headers', 'dataArr'));

        $response = json_decode(\App\Helpers\NoonCurlHelper::post($api_url, $dataArr, $headers));

        \Log::channel('payment_logs')->info('Payment Init Response');
        \Log::channel('payment_logs')->info(json_encode($response));

        if (isset($response->resultCode) && $response->resultCode == 0) {
            return (object) ['err' => false, 'message' => @$response->result->order->errorMessage, 'data' => $response];
        }

        return (object) ['err' => true, 'message' => @$response->message, 'data' => $response];
    }

    public function getResponse($order_id)
    {
        $api_url = env('NOON_PAYMENT_PAYMENT_API') . "order/{$order_id}";
        $headers = $this->getHeaders();

        $response = json_decode(\App\Helpers\NoonCurlHelper::get($api_url, $headers));

        \Log::channel('payment_logs')->info('Payment Status Request Response');
        \Log::channel('payment_logs')->info(json_encode($response));

        if (!(@$response->result->transactions[0]->type == "SALE" && @$response->result->transactions[0]->status == "SUCCESS")) {
            return (object) ['err' => true, 'message' => @$response->result->order->errorMessage, 'data' => $response];
        }

        return (object) ['err' => false, 'data' => $response];
    }

    public function refund($paymentData)
    {
        $api_url = env('NOON_PAYMENT_PAYMENT_API') . 'order';
        $headers = $this->getHeaders();
        $dataArr = [
            'apiOperation' => 'REFUND',
            'order' => [
                'id' => $paymentData['order_id'], // Order Id provided in the 'INITIATE' api response. 
            ],
            'transaction' => [
                'currency' => 'SAR',
                'amount' => $paymentData['amount'],
                // 'description' => '',
                // 'targetTransactionId' => '' // The capture transaction Id to link this refund with the capture. 
            ],
        ];
        // dd(compact('api_url', 'headers', 'dataArr'));
        \Log::channel('payment_logs')->info('Payment Init Request');
        \Log::channel('payment_logs')->info(json_encode(compact('api_url', 'headers', 'dataArr')));

        $response = json_decode(\App\Helpers\NoonCurlHelper::post($api_url, $dataArr, $headers));

        \Log::channel('payment_logs')->info('Payment Init Response');
        \Log::channel('payment_logs')->info(json_encode($response));

        if (isset($response->resultCode) && $response->resultCode == 0) {
            return (object) ['err' => false, 'data' => $response];
        }

        return (object) ['err' => true, 'message' => @$response->message, 'data' => $response];
    }
}
