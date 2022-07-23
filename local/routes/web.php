<?php

use App\Http\Controllers\Admin\AutoAddressController;
use App\Http\Controllers\Admin\ChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

/*
 *****************************
 ****** Payment Routes *******
 *****************************
 */

Route::group(['prefix' => 'noon-payment'], function () {
    Route::get('pay', 'PaymentController@getInit');
    Route::get('response', [
        'uses' => 'PaymentController@getResponse',
        'as' => 'payment.response',
    ]);

    Route::get('/subscription/response', [
        'uses' => 'PaymentController@getSubscriptionResponse',
        'as' => 'payment.subscription.response',
    ]);
    Route::get('/success', [
        'uses' => 'PaymentController@getSuccess',
        'as' => 'payment.success',
    ]);
    Route::get('/failed', [
        'uses' => 'PaymentController@getFailed',
        'as' => 'payment.failed',
    ]);
});

/*
 *****************************
 *  Start Website Routes
 *****************************
 */
Route::group(['namespace' => 'Website'], function () {
    Route::get('/', [
        'uses' => 'HomeController@getIndex',
        'as' => 'website.home',
    ]);
    Route::get('/terms-conditions/{lang?}', [
        'uses' => 'HomeController@getTermsConditions',
        'as' => 'website.terms_conditions',
    ])->where('lang', 'en|ar');
    Route::get('/privacy-policy/{lang?}', [
        'uses' => 'HomeController@getPrivacyPolicy',
        'as' => 'website.privacy_policy',
    ])->where('lang', 'en|ar');
    Route::get('/about-us/{lang?}', [
        'uses' => 'HomeController@getAboutUs',
        'as' => 'website.about_us',
    ])->where('lang', 'en|ar');
    Route::get('/cancellation-policy/{lang?}', [
        'uses' => 'HomeController@getCancellationPolicy',
        'as' => 'website.cancellation_policy',
    ])->where('lang', 'en|ar');
    Route::get('/vendor/privacy-policy/{lang?}', [
        'uses' => 'HomeController@getVendorPrivacyPolicy',
        'as' => 'website.vendor.privacy_policy',
    ])->where('lang', 'en|ar');
    Route::get('/vendor/terms-conditions/{lang?}', [
        'uses' => 'HomeController@getVendorTermsConditions',
        'as' => 'website.vendor.terms_conditions',
    ])->where('lang', 'en|ar');
    Route::get('/vendor/about-us/{lang?}', [
        'uses' => 'HomeController@getVendorAboutUs',
        'as' => 'website.vendor.about_us',
    ])->where('lang', 'en|ar');
    Route::get('/categories', [
        'uses' => 'HomeController@getCategories',
        'as' => 'website.categories',
    ]);
    Route::get('/change/locale/{lang}', [
        'uses' => 'HomeController@getChangeLocale',
        'as' => 'website.change.locale',
    ]);
    Route::post('send/download/mobile/app/link', [
        'uses' => 'HomeController@postSendDownloadLink',
        'as' => 'website.send.download.mobile_app',
    ]);
    Route::get('/download/mobile/app', [
        'uses' => 'HomeController@getDownloadMobileApp',
        'as' => 'website.download.mobile_app',
    ]);
    Route::get('/product/{id}', [
        'uses' => 'HomeController@getDownloadMobileApp',
        'as' => 'website.share.product',
    ]);
    Route::get('/service/{id}', [
        'uses' => 'HomeController@getDownloadMobileApp',
        'as' => 'website.share.service',
    ]);
    Route::get('/contact-us', [
        'uses' => 'HomeController@getContactUs',
        'as' => 'website.contact_us',
    ]);
    Route::post('/contact-us', [
        'uses' => 'HomeController@postContactUs',
        'as' => 'website.contact_us',
    ]);
});

/*
 *****************************
 *  Start Admin Routes
 *****************************
 */
Route::group(['namespace' => 'Admin', 'middleware' => 'assign.guard:admin'], function () {
    Route::get('login/change/locale', [
        'uses' => 'AuthController@getChangeLocale',
        'as' => 'admin.login.change.locale',
    ]);
});

Route::group(['namespace' => 'Admin', 'prefix' => env('URL_PREFIX'), 'middleware' => 'assign.guard:admin'], function () {

    /* Start Unsecured Routes */
    Route::get('/', [
        'uses' => 'AuthController@getLogin',
        'as' => 'login',
    ]);
    Route::get('/login', [
        'uses' => 'AuthController@getLogin',
        'as' => 'admin.login',
    ]);
    Route::post('/login', [
        'uses' => 'AuthController@postLogin',
        'as' => 'admin.login',
    ]);
    Route::get('/forgot/password', [
        'uses' => 'AuthController@getForgotPassword',
        'as' => 'admin.forgot.password',
    ]);
    Route::post('/forgot/password', [
        'uses' => 'AuthController@postForgotPassword',
        'as' => 'admin.forgot.password',
    ]);
    Route::get('/password/reset/request/{token}', [
        'uses' => 'AuthController@getResetPassword',
        'as' => 'admin.password.reset.request',
    ]);
    Route::post('/password/reset', [
        'uses' => 'AuthController@postResetPassword',
        'as' => 'admin.password.reset',
    ]);
    /* End Unsecured Routes */

    // Secured Routes
    Route::group(['middleware' => ['auth:admin', 'optimizeImages']], function () {
        /* Start Cropper Routes */
        Route::get('/cropper/init/{width}/{height}/{name}/{enable_ratio}', [
            'uses' => 'CropperController@getIndex',
            'as' => 'admin.image.cropper',
        ]);

        /* End Cropper Routes */

        /* Start Address Picker Routes */
        Route::get('/address/picker/{latitude?}/{longitude?}', [
            'uses' => 'DashboardController@getAddressPicker',
            'as' => 'admin.address.picker',
        ]);
        /* End Address Picker Routes */

        /* Start Dashboard Routes */
        Route::get('/dashboard', [
            'uses' => 'DashboardController@getIndex',
            'as' => 'admin.dashboard',
        ]);
        Route::get('/dashboard/stats', [
            'uses' => 'DashboardController@getStats',
            'as' => 'admin.dashboard.stats',
        ]);
        Route::get('/dashboard/earnings/graph', [
            'uses' => 'DashboardController@getEarningsGraph',
            'as' => 'admin.dashboard.earnings.graph',
        ]);
        Route::get('/dashboard/product_bookings/graph', [
            'uses' => 'DashboardController@getProductBookingsGraph',
            'as' => 'admin.dashboard.product_bookings.graph',
        ]);
        Route::get('/dashboard/service_bookings/graph', [
            'uses' => 'DashboardController@getServiceBookingsGraph',
            'as' => 'admin.dashboard.service_bookings.graph',
        ]);
        Route::get('/dashboard/users/graph', [
            'uses' => 'DashboardController@getUsersGraph',
            'as' => 'admin.dashboard.users.graph',
        ]);
        Route::get('/dashboard/vendors/graph', [
            'uses' => 'DashboardController@getVendorsGraph',
            'as' => 'admin.dashboard.vendors.graph',
        ]);
        Route::get('/dashboard/product/graph', [
            'uses' => 'DashboardController@getProductsGraph',
            'as' => 'admin.dashboard.products.graph',
        ]);
        Route::get('/change/locale', [
            'uses' => 'DashboardController@getChangeLocale',
            'as' => 'admin.change.locale',
        ]);
        /* End Dashboard Routes */


        /* Start Profile Routes */
        Route::get('/profile', [
            'uses' => 'ProfileController@getDetails',
            'as' => 'admin.profile.details',
        ]);
        Route::post('/profile', [
            'uses' => 'ProfileController@postUpdate',
            'as' => 'admin.profile.update',
        ]);
        Route::post('/profile/change_password', [
            'uses' => 'ProfileController@postChangePassword',
            'as' => 'admin.profile.change_password',
        ]);
        Route::get('/logout', [
            'uses' => 'ProfileController@getLogout',
            'as' => 'admin.logout',
        ]);
        /* End Profile Routes */


        /* Start Sub Admin Routes */
        Route::get('sub_admins', [
            'uses' => 'SubAdminController@getIndex',
            'as' => 'admin.sub_admins.index',
        ]);
        Route::get('sub_admins/create', [
            'uses' => 'SubAdminController@getCreate',
            'as' => 'admin.sub_admins.create',
        ]);
        Route::post('sub_admins/create', [
            'uses' => 'SubAdminController@postCreate',
            'as' => 'admin.sub_admins.create',
        ]);
        Route::get('sub_admins/list', [
            'uses' => 'SubAdminController@getList',
            'as' => 'admin.sub_admins.list',
        ]);
        Route::get('sub_admins/update/{id?}', [
            'uses' => 'SubAdminController@getUpdate',
            'as' => 'admin.sub_admins.update',
        ]);
        Route::post('sub_admins/update/{id?}', [
            'uses' => 'SubAdminController@postUpdate',
            'as' => 'admin.sub_admins.update',
        ]);
        Route::get('sub_admins/delete/{id?}', [
            'uses' => 'SubAdminController@getDelete',
            'as' => 'admin.sub_admins.delete',
        ]);
        Route::get('sub_admins/view/{id?}', [
            'uses' => 'SubAdminController@getView',
            'as' => 'admin.sub_admins.view',
        ]);
        Route::post('sub_admins/reset-password/{id?}', [
            'uses' => 'SubAdminController@postPasswordReset',
            'as' => 'admin.sub_admins.password_reset',
        ]);
        Route::get('sub_admins/reset-password/{id?}', [
            'uses' => 'SubAdminController@getPasswordReset',
            'as' => 'admin.sub_admins.password_reset',
        ]);
        /* End Sub Admin Routes */


        /* Start Navigation Routes */
        Route::get('navigation', [
            'uses' => 'NavigationController@getIndex',
            'as' => 'admin.navigation.index',
        ]);
        Route::get('navigation/create', [
            'uses' => 'NavigationController@getCreate',
            'as' => 'admin.navigation.create',
        ]);
        Route::post('navigation/create', [
            'uses' => 'NavigationController@postCreate',
            'as' => 'admin.navigation.create',
        ]);
        Route::get('navigation/list', [
            'uses' => 'NavigationController@getList',
            'as' => 'admin.navigation.list',
        ]);
        Route::get('navigation/update/{id?}', [
            'uses' => 'NavigationController@getUpdate',
            'as' => 'admin.navigation.update',
        ]);
        Route::post('navigation/update/{id?}', [
            'uses' => 'NavigationController@postUpdate',
            'as' => 'admin.navigation.update',
        ]);
        /* End Navigation Routes */


        /* Start Role Routes */
        Route::get('role', [
            'uses' => 'RoleController@getIndex',
            'as' => 'admin.role.index',
        ]);
        Route::get('role/create', [
            'uses' => 'RoleController@getCreate',
            'as' => 'admin.role.create',
        ]);
        Route::post('role/create', [
            'uses' => 'RoleController@postCreate',
            'as' => 'admin.role.create',
        ]);
        Route::get('role/list', [
            'uses' => 'RoleController@getList',
            'as' => 'admin.role.list',
        ]);
        Route::get('role/update/{id?}', [
            'uses' => 'RoleController@getUpdate',
            'as' => 'admin.role.update',
        ]);
        Route::post('role/update/{id?}', [
            'uses' => 'RoleController@postUpdate',
            'as' => 'admin.role.update',
        ]);
        Route::get('role/permission/{id?}', [
            'uses' => 'RoleController@getPermission',
            'as' => 'admin.role.permission',
        ]);
        Route::post('role/permission/{id?}', [
            'uses' => 'RoleController@savePermission',
            'as' => 'admin.role.permission.save',
        ]);
        /* End Role Routes */


        /* Start User Routes */
        Route::get('users', [
            'uses' => 'UserController@getIndex',
            'as' => 'admin.users.index',
        ]);
        Route::get('users/list', [
            'uses' => 'UserController@getList',
            'as' => 'admin.users.list',
        ]);
        Route::get('users/create', [
            'uses' => 'UserController@getCreate',
            'as' => 'admin.users.create',
        ]);
        Route::post('users/create', [
            'uses' => 'UserController@postCreate',
            'as' => 'admin.users.create',
        ]);
        Route::get('users/update/{id?}', [
            'uses' => 'UserController@getUpdate',
            'as' => 'admin.users.update',
        ]);
        Route::post('users/update/{id?}', [
            'uses' => 'UserController@postUpdate',
            'as' => 'admin.users.update',
        ]);
        Route::get('users/delete/{id?}', [
            'uses' => 'UserController@getDelete',
            'as' => 'admin.users.delete',
        ]);
        Route::get('users/view/{id?}', [
            'uses' => 'UserController@getView',
            'as' => 'admin.users.view',
        ]);
        Route::post('users/reset-password/{id?}', [
            'uses' => 'UserController@postPasswordReset',
            'as' => 'admin.users.password_reset',
        ]);
        Route::get('users/reset-password/{id?}', [
            'uses' => 'UserController@getPasswordReset',
            'as' => 'admin.users.password_reset',
        ]);
        /* End User Routes */

        // location map routes

        Route::get('auto-complete-address', [AutoAddressController::class, 'googleAutoAddress']);
        Route::get('chat', [
            'uses' => 'ChatController@chat',
            'as' => 'admin.chats.chat',
        ]);

        /* Start App Settings Routes */
        Route::get('settings', [
            'uses' => 'SettingController@getIndex',
            'as' => 'admin.settings.index',
        ]);

        Route::post('settings', [
            'uses' => 'SettingController@postUpdate',
            'as' => 'admin.settings.update',
        ]);
        /* End App Settings Routes */


        /* Start Countries Routes */
        Route::get('countries', [
            'uses' => 'CountryController@getIndex',
            'as' => 'admin.countries.index',
        ]);
        Route::get('countries/list', [
            'uses' => 'CountryController@getList',
            'as' => 'admin.countries.list',
        ]);
        Route::get('countries/create', [
            'uses' => 'CountryController@getCreate',
            'as' => 'admin.countries.create',
        ]);
        Route::post('countries/create', [
            'uses' => 'CountryController@postCreate',
            'as' => 'admin.countries.create',
        ]);
        Route::get('countries/update/{id?}', [
            'uses' => 'CountryController@getUpdate',
            'as' => 'admin.countries.update',
        ]);
        Route::post('countries/update/{id?}', [
            'uses' => 'CountryController@postUpdate',
            'as' => 'admin.countries.update',
        ]);
        Route::get('countries/delete/{id?}', [
            'uses' => 'CountryController@getDelete',
            'as' => 'admin.countries.delete',
        ]);
        /* End Countries Routes */


        /* Start App Banners Routes */
        Route::get('banners', [
            'uses' => 'BannerController@getIndex',
            'as' => 'admin.banners.index',
        ]);
        Route::get('banners/list', [
            'uses' => 'BannerController@getList',
            'as' => 'admin.banners.list',
        ]);
        Route::get('banners/create', [
            'uses' => 'BannerController@getCreate',
            'as' => 'admin.banners.create',
        ]);
        Route::post('banners/create', [
            'uses' => 'BannerController@postCreate',
            'as' => 'admin.banners.create',
        ]);
        Route::get('banners/update/{id?}', [
            'uses' => 'BannerController@getUpdate',
            'as' => 'admin.banners.update',
        ]);
        Route::post('banners/update/{id?}', [
            'uses' => 'BannerController@postUpdate',
            'as' => 'admin.banners.update',
        ]);
        Route::get('banners/delete/{id?}', [
            'uses' => 'BannerController@getDelete',
            'as' => 'admin.banners.delete',
        ]);
        /* End App Banners Routes */


        /* Start Categories Routes */
        Route::get('categories', [
            'uses' => 'CategoryController@getIndex',
            'as' => 'admin.categories.index',
        ]);
        Route::get('categories/list', [
            'uses' => 'CategoryController@getList',
            'as' => 'admin.categories.list',
        ]);
        Route::get('categories/subcategories/list/{id?}', [
            'uses' => 'CategoryController@getSubCategoriesList',
            'as' => 'admin.categories.subcategories.list',
        ]);
        Route::get('categories/create', [
            'uses' => 'CategoryController@getCreate',
            'as' => 'admin.categories.create',
        ]);
        Route::post('categories/create', [
            'uses' => 'CategoryController@postCreate',
            'as' => 'admin.categories.create',
        ]);
        Route::get('categories/update/{id?}', [
            'uses' => 'CategoryController@getUpdate',
            'as' => 'admin.categories.update',
        ]);
        Route::post('categories/update/{id?}', [
            'uses' => 'CategoryController@postUpdate',
            'as' => 'admin.categories.update',
        ]);
        Route::get('categories/delete/{id?}', [
            'uses' => 'CategoryController@getDelete',
            'as' => 'admin.categories.delete',
        ]);
        /* End Categories Routes */


        /* Start Vendors Routes */
        Route::get('vendors', [
            'uses' => 'VendorController@getIndex',
            'as' => 'admin.vendors.index',
        ]);
        Route::get('vendors/list', [
            'uses' => 'VendorController@getList',
            'as' => 'admin.vendors.list',
        ]);
        Route::get('vendors/create', [
            'uses' => 'VendorController@getCreate',
            'as' => 'admin.vendors.create',
        ]);
        Route::post('vendors/create', [
            'uses' => 'VendorController@postCreate',
            'as' => 'admin.vendors.create',
        ]);
        Route::get('vendors/update/{id?}', [
            'uses' => 'VendorController@getUpdate',
            'as' => 'admin.vendors.update',
        ]);
        Route::post('vendors/update/{id?}', [
            'uses' => 'VendorController@postUpdate',
            'as' => 'admin.vendors.update',
        ]);
        Route::post('vendors/approve/{id?}', [
            'uses' => 'VendorController@postApprove',
            'as' => 'admin.vendors.approve',
        ]);
        Route::get('vendors/reject/{id}', [
            'uses' => 'VendorController@getRejectApplication',
            'as' => 'admin.vendors.reject.index',
        ]);
        Route::post('vendors/reject/{id}', [
            'uses' => 'VendorController@postRejectApplication',
            'as' => 'admin.vendors.reject',
        ]);
        Route::get('vendors/reset/password/{id?}', [
            'uses' => 'VendorController@getPasswordReset',
            'as' => 'admin.vendors.reset_password',
        ]);
        Route::post('vendors/reset/password/{id?}', [
            'uses' => 'VendorController@postPasswordReset',
            'as' => 'admin.vendors.reset_password',
        ]);
        Route::get('vendors/delete/{id?}', [
            'uses' => 'VendorController@getDelete',
            'as' => 'admin.vendors.delete',
        ]);
        Route::get('vendors/view/{id?}', [
            'uses' => 'VendorController@getView',
            'as' => 'admin.vendors.view',
        ]);
        /* End Vendors Routes */


        /* Start Cities Routes */
        Route::get('cities', [
            'uses' => 'CityController@getIndex',
            'as' => 'admin.cities.index',
        ]);
        Route::get('cities/list', [
            'uses' => 'CityController@getList',
            'as' => 'admin.cities.list',
        ]);
        Route::get('cities/create', [
            'uses' => 'CityController@getCreate',
            'as' => 'admin.cities.create',
        ]);
        Route::post('cities/create', [
            'uses' => 'CityController@postCreate',
            'as' => 'admin.cities.create',
        ]);
        Route::get('cities/update/{id?}', [
            'uses' => 'CityController@getUpdate',
            'as' => 'admin.cities.update',
        ]);
        Route::post('cities/update/{id?}', [
            'uses' => 'CityController@postUpdate',
            'as' => 'admin.cities.update',
        ]);
        Route::get('cities/delete/{id?}', [
            'uses' => 'CityController@getDelete',
            'as' => 'admin.cities.delete',
        ]);
        /* End Cities Routes */


        /* Start Banks Routes */
        Route::get('banks', [
            'uses' => 'BankController@getIndex',
            'as' => 'admin.banks.index',
        ]);
        Route::get('banks/list', [
            'uses' => 'BankController@getList',
            'as' => 'admin.banks.list',
        ]);
        Route::get('banks/create', [
            'uses' => 'BankController@getCreate',
            'as' => 'admin.banks.create',
        ]);
        Route::post('banks/create', [
            'uses' => 'BankController@postCreate',
            'as' => 'admin.banks.create',
        ]);
        Route::get('banks/update/{id?}', [
            'uses' => 'BankController@getUpdate',
            'as' => 'admin.banks.update',
        ]);
        Route::post('banks/update/{id?}', [
            'uses' => 'BankController@postUpdate',
            'as' => 'admin.banks.update',
        ]);
        Route::get('banks/delete/{id?}', [
            'uses' => 'BankController@getDelete',
            'as' => 'admin.banks.delete',
        ]);
        /* End Banks Routes */


        /* Start Product Cancellation Questions Routes */
        Route::get('booking/cancellation-questions', [
            'uses' => 'CancellationQuestionController@getIndex',
            'as' => 'admin.booking_cancellation_questions.index',
        ]);
        Route::get('booking/cancellation-questions/list', [
            'uses' => 'CancellationQuestionController@getList',
            'as' => 'admin.booking_cancellation_questions.list',
        ]);
        Route::get('booking/cancellation-questions/create', [
            'uses' => 'CancellationQuestionController@getCreate',
            'as' => 'admin.booking_cancellation_questions.create',
        ]);
        Route::post('booking/cancellation-questions/create', [
            'uses' => 'CancellationQuestionController@postCreate',
            'as' => 'admin.booking_cancellation_questions.create',
        ]);
        Route::get('booking/cancellation-questions/update/{id?}', [
            'uses' => 'CancellationQuestionController@getUpdate',
            'as' => 'admin.booking_cancellation_questions.update',
        ]);
        Route::post('booking/cancellation-questions/update/{id?}', [
            'uses' => 'CancellationQuestionController@postUpdate',
            'as' => 'admin.booking_cancellation_questions.update',
        ]);
        Route::get('booking/cancellation-questions/delete/{id?}', [
            'uses' => 'CancellationQuestionController@getDelete',
            'as' => 'admin.booking_cancellation_questions.delete',
        ]);
        /* End Product Cancellation Questions Routes */


        /* Start Booking Questions Routes */
        Route::get('booking/questions', [
            'uses' => 'BookingQuestionController@getIndex',
            'as' => 'admin.booking_questions.index',
        ]);
        Route::get('booking/questions/list', [
            'uses' => 'BookingQuestionController@getList',
            'as' => 'admin.booking_questions.list',
        ]);
        Route::get('booking/questions/create', [
            'uses' => 'BookingQuestionController@getCreate',
            'as' => 'admin.booking_questions.create',
        ]);
        Route::post('booking/questions/create', [
            'uses' => 'BookingQuestionController@postCreate',
            'as' => 'admin.booking_questions.create',
        ]);
        Route::get('booking/questions/update/{id?}', [
            'uses' => 'BookingQuestionController@getUpdate',
            'as' => 'admin.booking_questions.update',
        ]);
        Route::post('booking/questions/update/{id?}', [
            'uses' => 'BookingQuestionController@postUpdate',
            'as' => 'admin.booking_questions.update',
        ]);
        Route::get('booking/questions/delete/{id?}', [
            'uses' => 'BookingQuestionController@getDelete',
            'as' => 'admin.booking_questions.delete',
        ]);
        Route::get('booking/questions/view/{id?}', [
            'uses' => 'BookingQuestionController@getView',
            'as' => 'admin.booking_questions.view',
        ]);
        /* End Booking Questions Routes */


        /* Start Inquiries Routes */
        Route::get('inquiry/users', [
            'uses' => 'InquiryController@getIndex',
            'as' => 'admin.inquiries.users.index',
        ]);
        Route::get('inquiry/vendors', [
            'uses' => 'InquiryController@getIndex',
            'as' => 'admin.inquiries.vendors.index',
        ]);
        Route::get('inquiry/list/{type}', [
            'uses' => 'InquiryController@getList',
            'as' => 'admin.inquiries.list',
        ]);
        Route::get('inquiry/delete/{id?}', [
            'uses' => 'InquiryController@getDelete',
            'as' => 'admin.inquiries.delete',
        ]);
        Route::get('inquiry/view/{id?}', [
            'uses' => 'InquiryController@getView',
            'as' => 'admin.inquiries.view',
        ]);
        Route::get('inquiry/send/email/{id?}', [
            'uses' => 'InquiryController@getSendEmail',
            'as' => 'admin.inquiries.send.email',
        ]);
        Route::post('inquiry/send/email/{id?}', [
            'uses' => 'InquiryController@postSendEmail',
            'as' => 'admin.inquiries.send.email',
        ]);
        Route::get('inquiry/send/notification/{id?}', [
            'uses' => 'InquiryController@getSendNotification',
            'as' => 'admin.inquiries.send.notification',
        ]);
        Route::post('inquiry/send/notification/{id?}', [
            'uses' => 'InquiryController@postSendNotification',
            'as' => 'admin.inquiries.send.notification',
        ]);
        /* End Inquiries Routes */


        /* Start Services Routes */
        Route::get('services', [
            'uses' => 'ServiceController@getIndex',
            'as' => 'admin.services.index',
        ]);
        Route::get('services/list', [
            'uses' => 'ServiceController@getList',
            'as' => 'admin.services.list',
        ]);

        Route::get('services/create', [
            'uses' => 'ServiceController@getCreate',
            'as' => 'admin.services.create',
        ]);
        Route::post('services/create', [
            'uses' => 'ServiceController@postCreate',
            'as' => 'admin.services.create',
        ]);
        Route::get('services/update/{id?}', [
            'uses' => 'ServiceController@getUpdate',
            'as' => 'admin.services.update',
        ]);
        Route::post('services/update/{id?}', [
            'uses' => 'ServiceController@postUpdate',
            'as' => 'admin.services.update',
        ]);
        Route::get('services/delete/{id?}', [
            'uses' => 'ServiceController@getDelete',
            'as' => 'admin.services.delete',
        ]);
        Route::get('services/view/{id?}', [
            'uses' => 'ServiceController@getView',
            'as' => 'admin.services.view',
        ]);
        /* End Services Routes */


        /* Start Services Images Routes */
        Route::get('services/images/create/{id?}', [
            'uses' => 'ServiceController@getImageCreate',
            'as' => 'admin.services.images.create',
        ]);
        Route::post('services/images/create/{id?}', [
            'uses' => 'ServiceController@postImageCreate',
            'as' => 'admin.services.images.create',
        ]);
        Route::post('services/image/order/update', [
            'uses' => 'ServiceController@postChangeImageOrder',
            'as' => 'admin.services.images.order.update',
        ]);
        Route::get('services/images/delete/{service_id}/{id}', [
            'uses' => 'ServiceController@getImageDelete',
            'as' => 'admin.services.images.delete',
        ]);
        /* End Services Images Routes */


        /* Start Services Reviews Routes */
        Route::get('services/reviews/list/{id?}', [
            'uses' => 'ServiceController@getReviewList',
            'as' => 'admin.services.reviews.list',
        ]);
        Route::get('services/reviews/delete/{id?}', [
            'uses' => 'ServiceController@getReviewDelete',
            'as' => 'admin.services.reviews.delete',
        ]);
        /* End Services Reviews Routes */


        /* Start Testimonial Routes */
        Route::get('testimonial', [
            'uses' => 'TestimonialController@getIndex',
            'as' => 'admin.testimonial.index',
        ]);
        Route::get('testimonial/list', [
            'uses' => 'TestimonialController@getList',
            'as' => 'admin.testimonial.list',
        ]);
        Route::get('testimonial/create', [
            'uses' => 'TestimonialController@getCreate',
            'as' => 'admin.testimonial.create',
        ]);
        Route::post('testimonial/create', [
            'uses' => 'TestimonialController@postCreate',
            'as' => 'admin.testimonial.create',
        ]);
        Route::get('testimonial/update/{id?}', [
            'uses' => 'TestimonialController@getUpdate',
            'as' => 'admin.testimonial.update',
        ]);
        Route::post('testimonial/update/{id?}', [
            'uses' => 'TestimonialController@postUpdate',
            'as' => 'admin.testimonial.update',
        ]);
        Route::get('testimonial/delete/{id?}', [
            'uses' => 'TestimonialController@getDelete',
            'as' => 'admin.testimonial.delete',
        ]);
        /* End Testimonial Routes */


        /* Start Products Routes */
        Route::get('products', [
            'uses' => 'ProductController@getIndex',
            'as' => 'admin.products.index',
        ]);
        Route::get('products/list', [
            'uses' => 'ProductController@getList',
            'as' => 'admin.products.list',
        ]);
        Route::get('products/create', [
            'uses' => 'ProductController@getCreate',
            'as' => 'admin.products.create',
        ]);
        Route::post('products/create', [
            'uses' => 'ProductController@postCreate',
            'as' => 'admin.products.create',
        ]);
        Route::get('products/update/{id?}', [
            'uses' => 'ProductController@getUpdate',
            'as' => 'admin.products.update',
        ]);
        Route::post('products/update/{id?}', [
            'uses' => 'ProductController@postUpdate',
            'as' => 'admin.products.update',
        ]);
        Route::get('products/delete/{id?}', [
            'uses' => 'ProductController@getDelete',
            'as' => 'admin.products.delete',
        ]);
        Route::get('products/view/{id?}', [
            'uses' => 'ProductController@getView',
            'as' => 'admin.products.view',
        ]);
        /* End Products Routes */


        /* Start Products Images Routes */
        Route::get('products/images/create/{id?}', [
            'uses' => 'ProductController@getImageCreate',
            'as' => 'admin.products.images.create',
        ]);
        Route::post('products/images/create/{id?}', [
            'uses' => 'ProductController@postImageCreate',
            'as' => 'admin.products.images.create',
        ]);
        Route::post('products/image/order/update', [
            'uses' => 'ProductController@postChangeImageOrder',
            'as' => 'admin.products.images.order.update',
        ]);
        Route::get('products/images/delete/{product_id}/{id}', [
            'uses' => 'ProductController@getImageDelete',
            'as' => 'admin.products.images.delete',
        ]);
        /* End Products Images Routes */


        /* Start Products Reviews Routes */
        Route::get('products/reviews/list/{id?}', [
            'uses' => 'ProductController@getReviewList',
            'as' => 'admin.products.reviews.list',
        ]);
        Route::get('products/reviews/delete/{id?}', [
            'uses' => 'ProductController@getReviewDelete',
            'as' => 'admin.products.reviews.delete',
        ]);
        /* End Products Reviews Routes */


        /* Start Payment History Routes */
        Route::get('vendor/payment/stats/{id}', [
            'uses' => 'VendorController@getPaymentStats',
            'as' => 'admin.vendors.payment.stats',
        ]);
        Route::get('vendor/payment/list/{id}', [
            'uses' => 'VendorController@getPaymentHistoryList',
            'as' => 'admin.vendors.payment.list',
        ]);
        Route::get('vendor/payment/create/{id?}', [
            'uses' => 'VendorController@getPaymentHistoryCreate',
            'as' => 'admin.vendors.payment.create',
        ]);
        Route::post('vendor/payment/create/{id?}', [
            'uses' => 'VendorController@postPaymentHistoryCreate',
            'as' => 'admin.vendors.payment.create',
        ]);
        Route::get('vendor/payment/update/{id?}', [
            'uses' => 'VendorController@getPaymentHistoryUpdate',
            'as' => 'admin.vendors.payment.update',
        ]);
        Route::post('vendor/payment/update/{id?}', [
            'uses' => 'VendorController@postPaymentHistoryUpdate',
            'as' => 'admin.vendors.payment.update',
        ]);
        Route::get('vendor/payment/delete/{id?}', [
            'uses' => 'VendorController@getPaymentHistoryDelete',
            'as' => 'admin.vendors.payment.delete',
        ]);
        /* End Payment  History Routes */


        /* Start Products Booking Routes */
        Route::get('products/booking', [
            'uses' => 'ProductBookingController@getIndex',
            'as' => 'admin.product_bookings.index',
        ]);
        Route::get('products/booking/list/{id?}', [
            'uses' => 'ProductBookingController@getList',
            'as' => 'admin.product_bookings.list',
        ]);
        Route::get('products/booking/view/{id?}', [
            'uses' => 'ProductBookingController@getView',
            'as' => 'admin.product_bookings.view',
        ]);
        Route::get('products/booking/delete/{id?}', [
            'uses' => 'ProductBookingController@getDelete',
            'as' => 'admin.product_bookings.delete',
        ]);
        Route::get('products/booking/cancel/{id}', [
            'uses' => 'ProductBookingController@getCancel',
            'as' => 'admin.product_bookings.cancel.index',
        ]);
        Route::post('products/booking/cancel/{id}', [
            'uses' => 'ProductBookingController@postCancel',
            'as' => 'admin.product_bookings.cancel',
        ]);
        Route::get('products/booking/refund/{id}', [
            'uses' => 'ProductBookingController@getProcessRefund',
            'as' => 'admin.product_bookings.refund',
        ]);
        Route::post('products/booking/refund/{id}', [
            'uses' => 'ProductBookingController@postProcessRefund',
            'as' => 'admin.product_bookings.refund',
        ]);
        Route::get('products/booking/refund/cancellation/{id}', [
            'uses' => 'ProductBookingController@getRefundCancellation',
            'as' => 'admin.product_bookings.refund.cancellation',
        ]);
        /* End Products Booking Routes */


        /* Start Notification Routes */
        Route::get('notifications', [
            'uses' => 'NotificationController@getIndex',
            'as' => 'admin.notifications.index',
        ]);
        Route::post('notifications/users/list', [
            'uses' => 'NotificationController@getUsersList',
            'as' => 'admin.notifications.users.list',
        ]);
        Route::post('notifications/vendors/list', [
            'uses' => 'NotificationController@getVendorsList',
            'as' => 'admin.notifications.vendors.list',
        ]);
        Route::post('notifications/send', [
            'uses' => 'NotificationController@postSend',
            'as' => 'admin.notifications.send',
        ]);
        /* End Notification Routes */


        /* Start Service Booking Routes */
        Route::get('service/bookings', [
            'uses' => 'ServiceBookingController@getIndex',
            'as' => 'admin.service_bookings.index',
        ]);
        Route::get('service/bookings/list/{id?}', [
            'uses' => 'ServiceBookingController@getList',
            'as' => 'admin.service_bookings.list',
        ]);
        Route::get('service/bookings/view/{id?}', [
            'uses' => 'ServiceBookingController@getView',
            'as' => 'admin.service_bookings.view',
        ]);
        Route::get('service/bookings/delete/{id?}', [
            'uses' => 'ServiceBookingController@getDelete',
            'as' => 'admin.service_bookings.delete',
        ]);
        Route::get('service/bookings/cancel/{id}', [
            'uses' => 'ServiceBookingController@getCancel',
            'as' => 'admin.service_bookings.cancel.index',
        ]);
        Route::post('service/bookings/cancel/{id}', [
            'uses' => 'ServiceBookingController@postCancel',
            'as' => 'admin.service_bookings.cancel',
        ]);
        Route::get('service/booking/refund/cancellation/{id}', [
            'uses' => 'ServiceBookingController@getRefundAmount',
            'as' => 'admin.service_bookings.refund',
        ]);
        /* End Service Booking Routes */


        /* Start Disputes Routes */
        Route::get('disputes', [
            'uses' => 'DisputeController@getIndex',
            'as' => 'admin.disputes.index',
        ]);
        Route::get('disputes/list', [
            'uses' => 'DisputeController@getList',
            'as' => 'admin.disputes.list',
        ]);
        Route::get('disputes/delete/{id?}', [
            'uses' => 'DisputeController@getDelete',
            'as' => 'admin.disputes.delete',
        ]);
        Route::get('disputes/view/{id?}', [
            'uses' => 'DisputeController@getView',
            'as' => 'admin.disputes.view',
        ]);
        Route::get('disputes/reply/{id}', [
            'uses' => 'DisputeController@getPostReply',
            'as' => 'admin.disputes.post.reply',
        ]);
        Route::post('disputes/reply/{id}', [
            'uses' => 'DisputeController@postPostReply',
            'as' => 'admin.disputes.post.reply',
        ]);
        Route::get('disputes/close/{id}', [
            'uses' => 'DisputeController@getCloseTicket',
            'as' => 'admin.disputes.close.ticket',
        ]);
        /* End Disputes Routes */
    });
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
