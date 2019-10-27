<?php

namespace App\Jobs;

use App\Http\Repositories\OCR\OcrRepository;
use App\Http\Repositories\Wechat\TempMsgRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OCRforWechat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mediaMsg = null;
    private $tempMsgRepo = null;
    private $ocrRepo = null;

    /**
     * Create a new job instance.
     *
     * @param $mediaMsg
     */
    public function __construct($mediaMsg)
    {
        $this->mediaMsg = $mediaMsg;
        $this->tempMsgRepo = new TempMsgRepository();
        $this->ocrRepo = new OcrRepository();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $this->tempMsgRepo->getAccessToken() . '&media_id=' . $this->mediaMsg->media_id;
        $filePath = public_path('/images/' . $this->mediaMsg->media_id . '.jpeg');
        $this->tempMsgRepo->saveMedia($url, $filePath);
        if (file_exists($filePath)) {
            $text =  $this->ocrRepo->ocr($filePath);
            $tempMsg = '{
                   "touser":"'.$this->mediaMsg->from_user.'",
                   "template_id":"'.env("API_MONITOR_TEMP_ID").'",
                    "data":{
                        "text":{
                            "value":"'.$text.'"
                        }
                    }
               }';
            PushWechatTempMsg::dispatch($tempMsg);
            unlink($filePath);
        }
    }
}
