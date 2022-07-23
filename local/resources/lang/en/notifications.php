<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notifications Language Lines
    |--------------------------------------------------------------------------
     */
    
     
    'send_user_otp' => 'Please use :otp as one time password to authenticate your device at Mustajarati application. Please do not share with anyone.',
    'send_vendor_otp' => 'Please use :otp as one time password to authenticate your device at Mustajarati vendor application. Please do not share with anyone.',
    'app_share_link' => "Hi :) click on this link to install Mustajarati app on your phone\n\r:link",


    'vendor_approved_title' => 'Your Application Approved',
    'vendor_approved_content' => 'Your application successfully approved. Now you can start adding your products / services.',

    'vendor_rejected_title' => 'Your Application Rejected',
    'vendor_rejected_content' => 'Your application rejected due to :comments.',

    'product' => [
        'booking_accepted' => [
            'title' => 'Booking Accepted',
            'content' => 'Congratulations! Your request for :product has been accepted by vendor. For further details we will keep you updated.',
        ],
        'booking_cancelled' => [
            'title' => 'Booking Cancelled',
            'content' => 'Customer cancelled the booking #:booking_code. Sorry for the inconvenience caused.',
        ],
        'booking_rejected' => [
            'title' => 'Booking Rejected',
            'content' => 'Vendor cancelled your booking #:booking_code. Might be the item is not available please try with another vendor.',
        ],
        'handover_to_user' => [
            'title' => 'Receive Product - :product',
            'content' => 'Vendor wants a confirmation for the receipt of :product against booking #:booking_code, please respond to the request by answering few questions (if there is any).',
        ],
        'handover_to_vendor' => [
            'title' => 'Receive Product - :product',
            'content' => 'Customer confirmed the return of :product against booking #:booking_code, please check the product carefully and confirm the acceptance.',
        ],
        'booking_complete_to_user' => [
            'title' => 'Booking Completed',
            'content' => 'Thanks for using Mustajarati, your booking #:booking_code is closed. If there is any balance left to us we will initiate a refund to you, in case you owe then a payment link will be send to you. For any dispute kindly raise it through My Bookings.',
        ],
        'booking_complete_to_vendor' => [
            'title' => 'Booking Completed',
            'content' => 'Happy business with Mustajarati, booking #:booking_code has been closed. Wish you get more and more business with us.',
        ],
        'booking_cancellation_refunded' => [
            'title' => 'Refund Initiated for Booking',
            'content' => 'A refund of SAR :amount has been initiated against the booking #:booking_code. Please check our Refund Policy if there is any doubt in initiated amount.',
        ],
        'pending_amount_refunded' => [
            'title' => 'Refund for Booking',
            'content' => 'Dear :user, refund of SAR :amount against the booking #:booking_code has been processed, amount will be credit in your card within 72 bank working hours.',
        ],
        'pending_amount_from_customer' => [
            'title' => 'Booking Amount Pending',
            'content' => 'Dear :user, there is an outstanding of SAR :amount against the booking #:booking_code, kindly pay as earliest as possible.',
        ],
    ],

    'service' => [
        'booking_accepted' => [
            'title' => 'Booking Accepted',
            'content' => 'Congratulations! Your request for :service has been accepted by vendor. For further details we will keep you updated.',
        ],
        'booking_cancelled' => [
            'title' => 'Booking Cancelled',
            'content' => 'Customer cancelled the booking #:booking_code. Sorry for the inconvenience caused.',
        ],
        'booking_rejected' => [
            'title' => 'Booking Rejected',
            'content' => 'Vendor cancelled your booking #:booking_code. This vendor might be busy, please try with another vendor.',
        ],
        'booking_started' => [
            'title' => 'Booking Started',
            'content' => 'Vendor has started the service against booking #:booking_code, kindly confirm if it is actually started.',
        ],
        'booking_completed_to_user' => [
            'title' => 'Booking Completed',
            'content' => 'Vendor has completed the booking #:booking_code, if there is any balance left to us we will initiate a refund to you, in case you owe then a payment link will be send to you. For any dispute kindly raise it through My Bookings.',
        ],
        'booking_completed_to_vendor' => [
            'title' => 'Booking Completed',
            'content' => 'Happy business with Mustajarati, booking #:booking_code has been closed. Wish you get more and more business with us.',
        ],
        'booking_cancellation_refunded' => [
            'title' => 'Refund Initiated for Booking',
            'content' => 'A refund of SAR :amount has been initiated against the booking #:booking_code. Please check our Refund Policy if there is any doubt in initiated amount.',
        ],
    ]
];
