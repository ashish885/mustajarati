<?php

namespace App\Jobs\Emails\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CustomerRaisedDisputeJob implements ShouldQueue
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
            ['email' => $email, 'booking_code' => $booking_code, 'locale' => $locale] = $this->dataArr;

            $emailArr = [];
            $emailArr['support_email'] = getAppSetting('support_email');
            $emailArr['support_phone_no'] = getAppSetting('support_phone_no');
            $emailArr['facebook_url'] = getAppSetting('facebook_url');
            $emailArr['linkedin_url'] = getAppSetting('linkedin_url');
            $emailArr['instagram_url'] = getAppSetting('instagram_url');
            $emailArr['twitter_url'] = getAppSetting('twitter_url');
            $emailArr['company_address'] = getAppSetting('company_address');
            $emailArr['booking_code'] = $booking_code;
            $emailArr['template_lang'] = $locale;

            $subject = \Lang::choice('emails.vendors.customer_raised_dispute', null, [], $locale);

            \App\Jobs\SendEmailJob::dispatch('vendors.customer_raised_dispute', $subject, $email, $emailArr);
        } catch (\Exception $e) {
            \Log::error($e);
        } finally {
            return true;
        }
    }
}
