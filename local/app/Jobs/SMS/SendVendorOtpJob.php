<?php

namespace App\Jobs\SMS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendVendorOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataArr;

    /**
     * Create a new job instance.
     *
     * @return void
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

            $vendor = is_numeric($vendor) ? \App\Models\Vendor::find($vendor) : $vendor;
            if (!blank($vendor)) {
                $vendor->otp = generateOtp();
                $vendor->save();

                $message = \Lang::choice('notifications.send_vendor_otp', null, ['otp' => $vendor->otp], $locale);
                \App\Jobs\SendSMSJob::dispatch(['mobile' => "{$vendor->dial_code}{$vendor->mobile}", 'message' => $message]);
            }
        } catch (\Throwable $th) {
            \Log::error('Error while sending SMS to Vendor');
            \Log::error($th);
        } finally {
            return true;
        }
    }
}
