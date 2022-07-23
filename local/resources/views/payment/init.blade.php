<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment</title>
    <style>
        body {background-color:#f6f6f5;}
    </style>
</head>
<body>
    <form action="{{ route('payment.hyperpay.response', compact('locale', 'payment_for')) }}" class="paymentWidgets" data-brands="VISA MASTER"></form>
    <script src="{{ env('HYPERPAY_URL') }}/v1/paymentWidgets.js?checkoutId={{ $checkout_id }}"></script>
</body>
</html>