<?php

namespace App\Jobs\Notifications\Product\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProductReceivedByVendorJob implements ShouldQueue
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
                'title' => \Lang::choice('notifications.product.booking_complete_to_user.title', null, [], 'ar'),
                'en_title' => \Lang::choice('notifications.product.booking_complete_to_user.title', null, [], 'en'),
                'message' => \Lang::choice('notifications.product.booking_complete_to_user.content', null, ['booking_code' =>  $booking->booking_code], 'ar'),
                'en_message' => \Lang::choice('notifications.product.booking_complete_to_user.content', null, ['booking_code' =>  $booking->booking_code], 'en'),
                'attribute' => 'product_booking_id',
                'value' => $booking->id,
                'notification_type' => 2,
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
