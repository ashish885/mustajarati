<?php

namespace App\Jobs\Notifications\Product\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BookingAcceptedJob implements ShouldQueue
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

            $product = \App\Models\ProductBookingDetail::select(\DB::raw('name, en_name'))
                ->where('product_booking_id', $booking->id)
                ->first();
            
            \App\Jobs\SendNotificationJob::dispatch([
                'title' => \Lang::choice('notifications.product.booking_accepted.title', null, [], 'ar'),
                'en_title' => \Lang::choice('notifications.product.booking_accepted.title', null, [], 'en'),
                'message' => \Lang::choice('notifications.product.booking_accepted.content', null, ['product' =>  $product->name], 'ar'),
                'en_message' => \Lang::choice('notifications.product.booking_accepted.content', null, ['product' =>  $product->en_name], 'en'),
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
