<?php

namespace App\Jobs;

use App\Http\Repositories\OCR\OcrRepository;
use App\Http\Repositories\Wechat\TempMsgRepository;
use App\Http\Repositories\Wechat\WechatBaseRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class OCRforWechat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $msg        = null;
    private $wechatRepo = null;

    /**
     * Create a new job instance.
     *
     * @param $postObj
     * @param $msg
     */
    public function __construct($msg)
    {
        $this->msg = $msg;
        $this->wechatRepo = new WechatBaseRepository();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filePath = public_path('/images/' . $this->msg->media_id . '.jpeg');

        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $this->wechatRepo->saveMedia($this->msg->media_id, $filePath);
        if (file_exists($filePath)) {
            $ocrRepo = new OcrRepository();
            $text = $ocrRepo->ocr($filePath);
            $text = str_replace("\n", ' ', $text);
            $tempMsg = '{
                   "touser":"' . $this->msg->from_user . '",
                   "template_id":"' . env("OCR_TEMP_ID") . '",
                    "data":{
                        "text":{
                            "value":"' . $text . '"
                        }
                    }
               }';
            PushWechatTempMsg::dispatch($tempMsg);
            unlink($filePath);
        }
        return;
    }
}
