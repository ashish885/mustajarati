<?php

namespace App\Jobs\Notifications\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApplicationApprovedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dataArr;

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
            ['vendor_id' => $vendor_id] = $this->dataArr;

            \App\Jobs\SendNotificationJob::dispatch([
                'title' => \Lang::choice('notifications.vendor_approved_title', null, [], 'ar'),
                'en_title' => \Lang::choice('notifications.vendor_approved_title', null, [], 'en'),
                'message' => \Lang::choice('notifications.vendor_approved_content', null, [], 'ar'),
                'en_message' => \Lang::choice('notifications.vendor_approved_content', null, [], 'en'),
                'notification_type' => 1,
            ], [
                'to' => 'vendor',
                'id' => $vendor_id,
            ]);

        } catch (\Throwable $th) {
            \Log::error($th);
        } finally {
            return true;
        }
    }
}
