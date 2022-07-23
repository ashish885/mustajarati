<?php

namespace App\Jobs\Emails\Vendor\Product;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BookingCancelledJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataArr;

    /**
     * Create a new job instance.
     */
    public function __construct($dataArr)
    {
        $this->dataArr = $dataArr;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            ['booking' => $booking, 'locale' => $locale] = $this->dataArr;

            $email = \App\Models\Vendor::where('id', $booking->vendor_id)->value("email");

            $lang = $locale == 'ar' ? '' : "{$locale}_name";
            $item_name = \App\Models\ProductBookingDetail::where('product_booking_id', $booking->id)->value("{$lang}name");

            $emailArr = [];
            $emailArr['support_email'] = getAppSetting('support_email');
            $emailArr['support_phone_no'] = getAppSetting('support_phone_no');
            $emailArr['facebook_url'] = getAppSetting('facebook_url');
            $emailArr['linkedin_url'] = getAppSetting('linkedin_url');
            $emailArr['instagram_url'] = getAppSetting('instagram_url');
            $emailArr['twitter_url'] = getAppSetting('twitter_url');
            $emailArr['company_address'] = getAppSetting('company_address');
            $emailArr['booking_code'] = $booking->booking_code;
            $emailArr['item_name'] = $item_name;
            $emailArr['template_lang'] = $locale;

            $subject = \Lang::choice('emails.vendor.booking_cancelled', null, ['booking_code' => $booking->booking_code], $locale);

            \App\Jobs\SendEmailJob::dispatch('vendor.product.booking_cancelled', $subject, $email, $emailArr);
        } catch (\Exception $e) {
            \Log::error($e);
        } finally {
            return true;
        }
    }
}
