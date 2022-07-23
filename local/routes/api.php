<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'user/v1', 'namespace' => 'API\User\V1', 'middleware' => 'assign.guard:api_user'], function () {

    /* Start Auth Routes */
    Route::get('/countries/list', 'CountryController@getList');
    Route::post('/login', 'AuthController@postLogin');
    Route::post('/register', 'AuthController@postRegister');
    Route::post('/register/verify/otp', 'AuthController@postVerifyRegisterOtp');
    Route::post('/forgot/password', 'AuthController@postForgotPassword');
    Route::post('/otp/resend', 'AuthController@postResendOTP');
    Route::post('/otp/verify', 'AuthController@postVerifyOtp');
    Route::post('/reset/password', 'AuthController@postResetPassword');
    Route::get('/refresh/token', 'AuthController@getRefreshToken');
    Route::post('/logout', 'AuthController@getLogout');
    /* End Auth Routes */


    /* Start Dashboard Routes */
    Route::get('/dashboard/details', 'DashboardController@getDashboardStats');
    Route::get('/app/settings', 'DashboardController@getAppSettings');
    /* End Dashboard Routes */


    /* Start Product Routes */
    Route::get('/cities/list', 'CityController@getList');
    Route::get('/categories/list', 'CategoryController@getList');
    Route::get('/products/list', 'ProductController@getList');
    Route::get('/products/filter/data', 'ProductController@getFilterData');
    Route::get('/products/details', 'ProductController@getDetails');
    Route::get('/products/favorites/list', 'ProductController@getFavoritesList');
    Route::post('/products/favorites/add', 'ProductController@postAddFavorite');
    Route::post('/products/favorites/remove', 'ProductController@postRemoveFavorite');
    Route::get('/products/reviews/list', 'ProductController@getReviewsList');
    /* End Product Routes */


    /* Start Service Routes */
    Route::get('/services/list', 'ServiceController@getList');
    Route::get('/services/filter/data', 'ServiceController@getFilterData');
    Route::get('/services/details', 'ServiceController@getDetails');
    Route::get('/services/favorites/list', 'ServiceController@getFavoritesList');
    Route::post('/services/favorites/add', 'ServiceController@postAddFavorite');
    Route::post('/services/favorites/remove', 'ServiceController@postRemoveFavorite');
    Route::get('/services/reviews/list', 'ServiceController@getReviewsList');
    /* End Service Routes */


    /* Start Profile Routes */
    Route::get('/profile/details', 'ProfileController@getDetails');
    Route::post('/profile/update', 'ProfileController@postUpdateProfile');
    Route::post('/profile/update/image', 'ProfileController@postUpdateImage');
    Route::post('/profile/change/password', 'ProfileController@postUpdatePassword');
    Route::post('/profile/update/fcm', 'ProfileController@postUpdateFcmToken');
    Route::post('/submit/inquiry', 'InquiryController@postSubmit');
    /* End Profile Routes */


    /* Start Dispute Routes */
    Route::get('/disputes/list', 'DisputeController@getList');
    Route::get('/disputes/messages/list', 'DisputeController@getMessageList');
    Route::post('/disputes/submit', 'DisputeController@postAdd');
    Route::post('/disputes/submit/message', 'DisputeController@postMessage');
    /* End Dispute Routes */


    /* Start Product Booking Routes */
    Route::post('/products/booking/calculation', 'ProductBookingController@postBookingCalculation');
    Route::post('/products/booking/place', 'ProductBookingController@postBook');
    Route::post('/products/booking/generate/payment/url', 'ProductBookingController@generatePaymentUrl');
    Route::get('/products/booking/listing', 'ProductBookingController@getListing');
    Route::get('/products/booking/details', 'ProductBookingController@getDetails');
    Route::get('/products/booking/cancel/reasons/list', 'ProductBookingController@getCancellationReasonsList');
    Route::post('/products/booking/cancel', 'ProductBookingController@postCancel');
    Route::get('/products/booking/receive', 'ProductBookingController@getReceiveProduct');
    Route::post('/products/booking/receive', 'ProductBookingController@postReceiveProduct');
    Route::post('/products/booking/upload/image', 'ProductBookingController@postUploadImage');
    Route::post('/products/booking/return', 'ProductBookingController@postReturnProduct');
    Route::post('/products/booking/submit/review', 'ProductBookingController@postSubmitRating');
    /* End Product Booking Routes */


    /* Start Service Booking Routes */
    Route::post('/services/booking/calculation', 'ServiceBookingController@postBookingCalculation');
    Route::post('/services/booking/place', 'ServiceBookingController@postBook');
    Route::post('/services/booking/generate/payment/url', 'ServiceBookingController@generatePaymentUrl');
    Route::get('/services/booking/listing', 'ServiceBookingController@getListing');
    Route::get('/services/booking/details', 'ServiceBookingController@getDetails');
    Route::get('/services/booking/cancel/reasons/list', 'ServiceBookingController@getCancellationReasonsList');
    Route::post('/services/booking/cancel', 'ServiceBookingController@postCancel');
    Route::post('/services/booking/complete', 'ServiceBookingController@postComplete');
    Route::post('/services/booking/submit/review', 'ServiceBookingController@postSubmitRating');
    /* End Service Booking Routes */

    /* Start Notification Logic */
    Route::get('/notifications/list', 'NotificationController@getListing');
    Route::get('/notifications/delete', 'NotificationController@getDelete');
    Route::get('/notifications/delete/all', 'NotificationController@getDeleteAll');
    /* End Notification Logic */
});

Route::group(['prefix' => 'vendor/v1', 'namespace' => 'API\Vendor\V1', 'middleware' => 'assign.guard:api_vendor'], function () {

    /* Start Auth Routes */
    Route::get('/countries/list', 'CountryController@getList');
    Route::get('/banks/list', 'BankController@getList');
    Route::post('/login', 'AuthController@postLogin');
    Route::post('/register', 'AuthController@postRegister');
    Route::post('/register/verify/otp', 'AuthController@postVerifyRegisterOtp');
    Route::post('/forgot/password', 'AuthController@postForgotPassword');
    Route::post('/otp/resend', 'AuthController@postResendOTP');
    Route::post('/otp/verify', 'AuthController@postVerifyOtp');
    Route::post('/reset/password', 'AuthController@postResetPassword');
    Route::get('/refresh/token', 'AuthController@getRefreshToken');
    Route::post('/logout', 'AuthController@getLogout');
    /* End Auth Routes */


    /* Start Dashboard Routes */
    Route::get('/dashboard/booking/stats', 'DashboardController@getDashboardStats');
    Route::get('/app/settings', 'DashboardController@getAppSettings');
    Route::post('/upload/product/service/image', 'DashboardController@postUploadProductOrServiceImage');
    /* End Dashboard Routes */


    /* Start Product Routes */
    Route::get('/cities/list', 'CityController@getList');
    Route::get('/categories/list', 'CategoryController@getList');
    Route::get('/products/list', 'ProductController@getList');
    Route::get('/products/details', 'ProductController@getDetails');
    Route::post('/products/add', 'ProductController@postAdd');
    Route::post('/products/update', 'ProductController@postUpdate');
    Route::post('/products/delete', 'ProductController@postDelete');
    /* End Product Routes */


    /* Start Service Routes */
    Route::get('/services/list', 'ServiceController@getList');
    Route::get('/services/details', 'ServiceController@getDetails');
    Route::post('/services/add', 'ServiceController@postAdd');
    Route::post('/services/update', 'ServiceController@postUpdate');
    Route::post('/services/delete', 'ServiceController@postDelete');
    /* End Service Routes */


    /* Start Profile Routes */
    Route::get('/profile/details', 'ProfileController@getDetails');
    Route::post('/profile/resubmit', 'ProfileController@postResubmitInfo');
    Route::post('/profile/update', 'ProfileController@postUpdateProfile');
    Route::post('/profile/update/image', 'ProfileController@postUpdateImage');
    Route::post('/profile/change/password', 'ProfileController@postUpdatePassword');
    Route::post('/profile/update/fcm', 'ProfileController@postUpdateFcmToken');
    Route::get('/profile/bank/info', 'ProfileController@getBankInfo');
    Route::post('/profile/bank/update', 'ProfileController@postBankInfoUpdate');
    Route::get('/payment/history', 'ProfileController@getPaymentHistory');
    Route::get('/payment/withdrawal/request', 'ProfileController@getWithdrawalRequest');
    Route::post('/submit/inquiry', 'InquiryController@postSubmit');
    /* End Profile Routes */

    /* Start Notification Logic */
    Route::get('/notifications/list', 'NotificationController@getListing');
    Route::get('/notifications/delete', 'NotificationController@getDelete');
    Route::get('/notifications/delete/all', 'NotificationController@getDeleteAll');
    /* End Notification Logic */


    /* Start Dispute Routes */
    Route::get('/disputes/list', 'DisputeController@getList');
    Route::get('/disputes/messages/list', 'DisputeController@getMessageList');
    Route::post('/disputes/submit', 'DisputeController@postAdd');
    Route::post('/disputes/submit/message', 'DisputeController@postMessage');
    /* End Dispute Routes */


    /* Start Product Booking Routes */
    Route::get('/products/booking/listing', 'ProductBookingController@getList');
    Route::get('/products/booking/details', 'ProductBookingController@getDetails');
    Route::post('/products/booking/accept', 'ProductBookingController@postAccept');
    Route::post('/products/booking/cancel', 'ProductBookingController@postCancel');
    Route::post('/products/booking/upload/image', 'ProductBookingController@postUploadImage');
    Route::post('/products/booking/handover', 'ProductBookingController@postHandoverProduct');
    Route::get('/products/booking/receive', 'ProductBookingController@getReceiveProduct');
    Route::post('/products/booking/receive', 'ProductBookingController@postReceiveProduct');
    /* End Product Booking Routes */


    /* Start Service Booking Routes */
    Route::get('/services/booking/listing', 'ServiceBookingController@getListing');
    Route::get('/services/booking/details', 'ServiceBookingController@getDetails');
    Route::post('/services/booking/accept', 'ServiceBookingController@postAccept');
    Route::post('/services/booking/cancel', 'ServiceBookingController@postCancel');
    Route::post('/services/booking/start', 'ServiceBookingController@postStart');
    Route::post('/services/booking/complete', 'ServiceBookingController@postComplete');
    /* End Service Booking Routes */

    /* Start Notification Logic */
    Route::get('/notifications/list', 'NotificationController@getListing');
    Route::get('/notifications/delete', 'NotificationController@getDelete');
    Route::get('/notifications/delete/all', 'NotificationController@getDeleteAll');
    /* End Notification Logic */

    /* Start Subscription Logic */
    Route::get('/subscription/details', 'SubscriptionController@getCurrentPlanDetails');
    Route::get('/subscriptions/list', 'SubscriptionController@getList');
    Route::post('/subscriptions/sponsor/item/add', 'SubscriptionController@postAddSponsorItem');
    Route::post('/subscriptions/sponsor/item/remove', 'SubscriptionController@postRemoveSponsorItem');
    Route::post('/subscriptions/buy', 'SubscriptionController@postPurchase');
    /* End Subscription Logic */
});
