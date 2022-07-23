<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataArr;
    protected $sendTo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dataArr, $sendTo)
    {
        $this->dataArr = $dataArr;
        $this->sendTo = $sendTo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            \Log::info(json_encode($this->dataArr));
            \Log::info(json_encode($this->sendTo));
            // return true;

            // Start Transaction
            \DB::beginTransaction();

            // Save Notification In DB
            if (is_array($this->sendTo['id'])) {
                $notificationArr = [];

                $notification = [];
                $notification['title'] = $this->dataArr['title'];
                $notification['en_title'] = $this->dataArr['en_title'];
                $notification['message'] = $this->dataArr['message'];
                $notification['en_message'] = $this->dataArr['en_message'];
                $notification['attribute'] = $this->dataArr['attribute'];
                $notification['value'] = $this->dataArr['value'];
                $notification['notification_type'] = $this->dataArr['notification_type'];
                $notification['created_at'] = $notification['updated_at'] = date('Y-m-d H:i:s');

                foreach ($this->sendTo['id'] as $id) {
                    $notification[$this->sendTo['to'] . '_id'] = $id;
                    $notificationArr[] = $notification;
                }
                \App\Models\Notification::insert($notificationArr);

                // Send Notification
                $tokens = \App\Models\FcmToken::whereIn($this->sendTo['to'] . '_id', $this->sendTo['id'])->pluck('fcm_id')->toArray();
            } else {
                $notification = new \App\Models\Notification();
                $notification->title = $this->dataArr['title'];
                $notification->en_title = $this->dataArr['en_title'];
                $notification->message = $this->dataArr['message'];
                $notification->en_message = $this->dataArr['en_message'];
                $notification->attribute = @$this->dataArr['attribute'];
                $notification->value = @$this->dataArr['value'];
                $notification->notification_type = @$this->dataArr['notification_type'];
                $notification[$this->sendTo['to'] . '_id'] = $this->sendTo['id'];
                $notification->save();

                // Send Notification
                $tokens = \App\Models\FcmToken::where($this->sendTo['to'] . '_id', $this->sendTo['id'])->pluck('fcm_id')->toArray();
            }

            // Commit Transaction
            \DB::commit();

            if (count($tokens)) {
                \App\Jobs\SendPushNotificationJob::dispatch($this->dataArr, $tokens);
            }

            return true;
        } catch (\Exception $e) {
            // Rollback Transaction
            \DB::rollBack();
            \Log::error($e);
            return true;
        }
    }
}
