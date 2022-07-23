<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSMSJob implements ShouldQueue
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
            ['mobile' => $mobile, 'message' => $message] = $this->dataArr;

            $params = array();
            $params['Recipient'] = $mobile;
            $params['Body'] = $message;
            $params['SenderID'] = "MUSTAJARATI";
            $params['AppSid'] = "XmY63o369gt4KQnydSOYP66lT2Qcan";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://basic.unifonic.com/rest/SMS/messages');
            curl_setopt($ch, CURLOPT_POST, count($params));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);

            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($http_status == 500) {
                \Log::error('SMS Response');
                \Log::error('Failed to send SMS');
            }

            \Log::debug('SMS Response');
            \Log::debug(@json_decode($result, true));
        } catch (\Throwable $th) {
            \Log::error('Error while sending SMS');
            \Log::error($e);
        } finally {
            return true;
        }
    }
}
