<?php

namespace App\Jobs\Emails\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FundWithdrawalRequestJob implements ShouldQueue
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
            ['vendor' => $vendor, 'locale' => $locale] = $this->dataArr;

            $bank_details = \App\Models\VendorBankDetail::select(\DB::raw('bank_id, account_no, iban_no'))
                ->where('vendor_id', $vendor->id)
                ->first();
            if (!blank($bank_details)) {
                $lang = $locale == 'ar' ? '' : "{$locale}_name";
                $bank_details->bank_name = \App\Models\Bank::where('id', $bank_details->bank_id)->value("{$lang}name");
            }
            
            $emailArr = [];
            $emailArr['support_email'] = getAppSetting('support_email');
            $emailArr['support_phone_no'] = getAppSetting('support_phone_no');
            $emailArr['facebook_url'] = getAppSetting('facebook_url');
            $emailArr['linkedin_url'] = getAppSetting('linkedin_url');
            $emailArr['instagram_url'] = getAppSetting('instagram_url');
            $emailArr['twitter_url'] = getAppSetting('twitter_url');
            $emailArr['company_address'] = getAppSetting('company_address');
            $emailArr['bank_details'] = $bank_details;
            $emailArr['template_lang'] = $locale;

            $subject = \Lang::choice('emails.vendor.fund_withdrawal_request', null, [], $locale);

            \App\Jobs\SendEmailJob::dispatch('vendor.fund_withdrawal_request', $subject, $vendor->email, $emailArr);
        } catch (\Exception $e) {
            \Log::error($e);
        } finally {
            return true;
        }
    }
}
