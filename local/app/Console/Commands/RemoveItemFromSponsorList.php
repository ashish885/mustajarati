<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveItemFromSponsorList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove_expired_sponsor_items:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will remove product/service from sponsor list after sponsor end date.';

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

            \App\Models\Product::whereNotNull('sponsor_end_date')
                ->where('sponsor_end_date', '<', date('Y-m-d'))
                ->update([
                    'is_sponsored' => 0,
                    'sponsor_start_date' => null,
                    'sponsor_end_date' => null,
                ]);

            \App\Models\Service::whereNotNull('sponsor_end_date')
                ->where('sponsor_end_date', '<', date('Y-m-d'))
                ->update([
                    'is_sponsored' => 0,
                    'sponsor_start_date' => null,
                    'sponsor_end_date' => null,
                ]);

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
