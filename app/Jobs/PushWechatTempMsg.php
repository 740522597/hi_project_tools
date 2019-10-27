<?php

namespace App\Jobs;

use App\Http\Repositories\Wechat\TempMsgRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PushWechatTempMsg implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $tempMsgRepository = null;
    private $tempMsg = null;

    /**
     * Create a new job instance.
     *
     * @param $tempMsg
     */
    public function __construct($tempMsg)
    {
        $this->tempMsgRepository = new TempMsgRepository();
        $this->tempMsg = $tempMsg;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $accessToken = $this->tempMsgRepository->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
        $result = $this->tempMsgRepository->https_request($url, $this->tempMsg);
        dd($result);
    }
}
