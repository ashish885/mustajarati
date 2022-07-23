<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notification;
    protected $fcmIds;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notification, $fcmIds)
    {
        $this->notification = $notification;
        $this->fcmIds = $fcmIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            // \Log::debug('Notification Log');
            // \Log::debug(json_encode($this->notification));
            // \Log::debug(json_encode($this->fcmIds));

            $url = 'https://fcm.googleapis.com/fcm/send';
            $api_key = config('cms.fcm_legacy_key');

            $notification = [
                'title' => $this->notification['title'],
                'en_title' => $this->notification['en_title'],
                'body' => $this->notification['message'],
                'en_body' => $this->notification['en_message'],
                'attribute' => @$this->notification['attribute'],
                'value' => @$this->notification['value'],
                'notification_type' => @$this->notification['notification_type'],
            ];

            $arrayToSend = [
                'registration_ids' => is_array($this->fcmIds) ? $this->fcmIds : [$this->fcmIds],
                'priority' => "high",
                'data' => $notification,
                'content_available' => true,
            ];

            $headers = [
                'Content-Type:application/json',
                "Authorization:key={$api_key}",
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));

            $result = curl_exec($ch);
            if ($result === false) {
                $result = curl_error($ch);
            }
            curl_close($ch);

            \Log::debug('FCM Response');
            \Log::debug($result);

            return $result;
        } catch (\Exception $e) {
            \Log::error($e);
            return true;
        }
    }
}
