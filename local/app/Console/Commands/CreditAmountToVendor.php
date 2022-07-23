<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreditAmountToVendor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credit:amount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'If return time is over then credit amount to vendor wallet';

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

            // For Product Bookings
            $bookings = \App\Models\ProductBooking::where('is_settled', 0)
                ->where('status', 3)
                ->whereNotNull('dispute_end_date')
                ->whereDate('dispute_end_date', '<', date('Y-m-d'))
                ->get();
            if ($bookings->isNotEmpty()) {
                foreach ($bookings as $booking) {
                    $vendor = \App\Models\Vendor::find($booking->vendor_id);
                    if (!blank($vendor)) {
                        $vendor->total_amount += $booking->vendor_amount;
                        $vendor->total_pending_amount += $booking->vendor_amount;
                        $vendor->save();
                    }

                    $booking->is_settled = 1;
                    $booking->save();
                }
            }

            // For Service Bookings
            $bookings = \App\Models\ServiceBooking::where('is_settled', 0)
                ->where('status', 3)
                ->whereNotNull('dispute_end_date')
                ->whereDate('dispute_end_date', '<', date('Y-m-d'))
                ->get();
            if ($bookings->isNotEmpty()) {
                foreach ($bookings as $booking) {
                    $vendor = \App\Models\Vendor::find($booking->vendor_id);
                    if (!blank($vendor)) {
                        $vendor->total_amount += $booking->vendor_amount;
                        $vendor->total_pending_amount += $booking->vendor_amount;
                        $vendor->save();
                    }

                    $booking->is_settled = 1;
                    $booking->save();
                }
            }

            // Commit Transaction
            \DB::commit();
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            \Log::error($e);
        }
        finally {
            return true;
        }
    }
}
