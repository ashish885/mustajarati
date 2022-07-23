<?php

namespace App\Jobs\Notifications\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApplicationRejectedJob implements ShouldQueue
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
            ['vendor_id' => $vendor_id, 'comments' => $comments] = $this->dataArr;

            \App\Jobs\SendNotificationJob::dispatch([
                'title' => \Lang::choice('notifications.vendor_rejected_title', null, [], 'ar'),
                'en_title' => \Lang::choice('notifications.vendor_rejected_title', null, [], 'en'),
                'message' => \Lang::choice('notifications.vendor_rejected_content', null, compact('comments'), 'ar'),
                'en_message' => \Lang::choice('notifications.vendor_rejected_content', null, compact('comments'), 'en'),
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
