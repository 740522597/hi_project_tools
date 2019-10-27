<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-10-27
 * Time: 01:10
 */

namespace App\Http\Repositories\Wechat;

use App\Http\Repositories\OCR\OcrRepository;
use App\Http\Repositories\Wechat\WechatBaseRepository;
use App\Jobs\OCRforWechat;
use App\WechatUserMsg;

class MsgRepository extends WechatBaseRepository
{
    private $msg = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function receiveTypeText($postObj)
    {
        if (trim($postObj->Content) == 'OCR') {
            WechatUserMsg::query()
                ->where('open_id', $postObj->FromUserName)
                ->where('content', 'OCR')
                ->delete();
            $this->storeMsg($postObj);
            $content = '请发送清晰图片，目前可识别中英文.';
            $data = [
                'template'  => $this->textTemp(),
                'to_user'   => $postObj->FromUserName,
                'from_user' => $postObj->ToUserName,
                'time'      => time(),
                'content'   => $content
            ];
            return $this->replyText($data);
        }
        return null;
    }

    public function receiveTypeEvent($postObj)
    {
        if (strtolower($postObj->Event) == 'subscribe') {
            $data = [
                'template'  => $this->textTemp(),
                'to_user'   => $postObj->FromUserName,
                'from_user' => $postObj->ToUserName,
                'time'      => time(),
                'content'   => '欢迎关注!'
            ];
            return $this->replyText($data);
        }
        return null;
    }

    public function replyText($data)
    {
        return sprintf($data['template'], $data['to_user'], $data['from_user'], $data['time'], 'text', $data['content']);
    }

    public function receiveTypeImage($postObj)
    {
        $lastMsg = WechatUserMsg::query()
            ->where('open_id', $postObj->FromUserName)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastMsg && $lastMsg->content == 'OCR') {
            $lastMsg->delete();
            $this->storeMsg($postObj);
            OCRforWechat::dispatch($this->msg);
        }
        return null;
    }

    public function storeMsg($postObj)
    {
        $this->msg = WechatUserMsg::query()
            ->firstOrCreate([
                'open_id'     => $postObj->FromUserName,
                'from_user'   => $postObj->FromUserName,
                'to_user'     => $postObj->ToUserName,
                'create_time' => date('Y-m-d H:i:s', strtotime($postObj->CreateTime)),
                'content'     => $postObj->Content,
                'msg_type'    => strtolower($postObj->MsgType),
                'msg_id'      => strtolower($postObj->MsgId),
                'media_id'    => isset($postObj->MediaId) ? $postObj->MediaId : null,
                'pic_url'     => isset($postObj->PicUrl) ? $postObj->PicUrl : null
            ]);
    }

    public function textTemp()
    {
        return "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[%s]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
    }
}