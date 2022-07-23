<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateTotalItemBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_item_bookings:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will increase total bookings of products / services after dispute period.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // Start Transaction
            \DB::beginTransaction();

            $product_bookings = \App\Models\ProductBooking::where('status', 3)
                ->whereNotNull('dispute_end_date')
                ->whereDate('dispute_end_date', '<', date('Y-m-d'))
                ->pluck('product_id');
            if ($product_bookings->count()) {
                foreach ($product_bookings as $product_id) {
                    \App\Models\Product::where('id', $product_id)->increment('total_bookings');
                }
            }

            $service_bookings = \App\Models\ServiceBooking::where('status', 3)
                ->whereNotNull('dispute_end_date')
                ->whereDate('dispute_end_date', '<', date('Y-m-d'))
                ->pluck('service_id');
            if ($service_bookings->count()) {
                foreach ($service_bookings as $service_id) {
                    \App\Models\Service::where('id', $service_id)->increment('total_bookings');
                }
            }

            // Commit Transaction
            \DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            \Log::error($e);
        } finally {
            return true;
        }
    }
}
