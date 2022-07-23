<?php

namespace App\Jobs\SMS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendUserOtpJob implements ShouldQueue
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
            ['user' => $user, 'locale' => $locale] = $this->dataArr;

            $user = is_numeric($user) ? \App\Models\User::find($user) : $user;
            if (!blank($user)) {
                $user->otp = generateOtp();
                $user->save();

                $message = \Lang::choice('notifications.send_user_otp', null, ['otp' => $user->otp], $locale);
                \App\Jobs\SendSMSJob::dispatch(['mobile' => "{$user->dial_code}{$user->mobile}", 'message' => $message]);
            }
        } catch (\Throwable $th) {
            \Log::error('Error while sending SMS to User');
            \Log::error($th);
        } finally {
            return true;
        }
    }
}
