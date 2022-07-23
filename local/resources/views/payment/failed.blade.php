<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Failed</title>

    <style>
        body {
            text-align: center;
        }
    </style>
</head>
<body>
    <img src="{{ URL::to('backend/images/failed.png') }}" alt="Payment Failed">
    <h4>Payment Failed</h4>
    @if (isset($message) && !empty($message))
        <p>{!! $message !!}</p>
    @endif
</body>
</html>