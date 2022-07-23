<?php

namespace App\Jobs\Notifications\Service\Vendor;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BookingCompletedJob implements ShouldQueue
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
            ['booking' => $booking] = $this->dataArr;

            \App\Jobs\SendNotificationJob::dispatch([
                'title' => \Lang::choice('notifications.service.booking_completed_to_vendor.title', null, [], 'ar'),
                'en_title' => \Lang::choice('notifications.service.booking_completed_to_vendor.title', null, [], 'en'),
                'message' => \Lang::choice('notifications.service.booking_completed_to_vendor.content', null, ['booking_code' => $booking->booking_code], 'ar'),
                'en_message' => \Lang::choice('notifications.service.booking_completed_to_vendor.content', null, ['booking_code' => $booking->booking_code], 'en'),
                'attribute' => 'service_booking_id',
                'value' => $booking->id,
                'notification_type' => 3,
            ], [
                'to' => 'user',
                'id' => $booking->user_id,
            ]);

        } catch (\Throwable $th) {
            \Log::error($th);
        } finally {
            return true;
        }
    }
}
