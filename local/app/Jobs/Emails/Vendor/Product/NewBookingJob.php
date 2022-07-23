<?php

namespace App\Jobs\Emails\Vendor\Product;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewBookingJob implements ShouldQueue
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
            ['booking' => $booking, 'booking_details' => $booking_details, 'locale' => $locale] = $this->dataArr;

            $email = \App\Models\Vendor::where('id', $booking->vendor_id)->value("email");
            
            $emailArr = [];
            $emailArr['support_email'] = getAppSetting('support_email');
            $emailArr['support_phone_no'] = getAppSetting('support_phone_no');
            $emailArr['facebook_url'] = getAppSetting('facebook_url');
            $emailArr['linkedin_url'] = getAppSetting('linkedin_url');
            $emailArr['instagram_url'] = getAppSetting('instagram_url');
            $emailArr['twitter_url'] = getAppSetting('twitter_url');
            $emailArr['company_address'] = getAppSetting('company_address');
            $emailArr['booking'] = $booking;
            $emailArr['booking_details'] = $booking_details;
            $emailArr['lang'] = $locale == 'ar' ? '' : "{$locale}_";
            $emailArr['template_lang'] = $locale;

            $subject = \Lang::choice('emails.vendor.new_booking', null, ['booking_code' => $booking->booking_code], $locale);

            \App\Jobs\SendEmailJob::dispatch('vendor.product.new_booking', $subject, $email, $emailArr);
        } catch (\Exception $e) {
            \Log::error($e);
        } finally {
            return true;
        }
    }
}
