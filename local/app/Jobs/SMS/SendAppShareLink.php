<?php

namespace App\Jobs\SMS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppShareLink implements ShouldQueue
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
            ['dial_code' => $dial_code, 'mobile' => $mobile, 'locale' => $locale] = $this->dataArr;
            
            $link = route('website.download.mobile_app');
            $message = \Lang::choice('notifications.app_share_link', null, ['link' => $link], $locale);
            
            \App\Jobs\SendSMSJob::dispatch(['mobile' => "{$dial_code}{$mobile}", 'message' => $message]);
        } catch (\Throwable $th) {
            \Log::error('Error while sharing app link');
            \Log::error($th);
        } finally {
            return true;
        }
    }
}
