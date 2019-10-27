<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-10-27
 * Time: 15:07
 */

namespace App\Http\Controllers\Wechat;


use App\Http\Repositories\Wechat\WechatBaseRepository;
use App\WechatUserMsg;
use Illuminate\Http\Request;

class WechatController
{
    public function msgReceiver(Request $request)
    {
        $postArr = file_get_contents("php://input");    //php7.0只能用这种方式获取数据，之前的$GLOBALS['HTTP_RAW_POST_DATA']7.0版本不可用
        $postObj = simplexml_load_string($postArr);    //读取xml格式文件,记得安装php7.0-xml

        //接收关注事件推送：用户关注微信号后，将会受到一条“欢迎光临”的消息
        if(strtolower($postObj->MsgType) == 'event'){
            if(strtolower($postObj->Event) == 'subscribe'){
                $toUser		= $postObj->FromUserName;
                $fromUser	= $postObj->ToUserName;
                $time      = time();
                $msgType   = 'text';
                $content   = '欢迎光临！';
                $template  = "<xml>
                               <ToUserName><![CDATA[%s]]></ToUserName>
                               <FromUserName><![CDATA[%s]]></FromUserName>
                               <CreateTime>%s</CreateTime>
                               <MsgType><![CDATA[%s]]></MsgType>
                               <Content><![CDATA[%s]]></Content>
                                </xml>";
                $info= sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
                echo $info;
            }
        }

        if(strtolower($postObj->MsgType)=='text' && trim($postObj->Content)=='OCR')
        {
            $toUser   =$postObj->FromUserName;
            $fromUser =$postObj->ToUserName;

            WechatUserMsg::query()
                ->firstOrCreate([
                   'open_id' => $postObj->FromUserName,
                   'from_user' => $postObj->FromUserName,
                   'to_user' => $postObj->ToUserName,
                   'create_time' => date('Y-m-d H:i:s', strtotime($postObj->CreateTime)),
                   'content' => $postObj->Content,
                   'msg_type' => strtolower($postObj->MsgType)
                ]);

            $template  = "<xml>
                               <ToUserName><![CDATA[%s]]></ToUserName>
                               <FromUserName><![CDATA[%s]]></FromUserName>
                               <CreateTime>%s</CreateTime>
                               <MsgType><![CDATA[%s]]></MsgType>
                               <Content><![CDATA[%s]]></Content>
                                </xml>";
            $content = '请发送清晰图片，目前可识别中英文.';
            echo sprintf($template,$toUser,$fromUser,time(),'text', $content);
        }
    }


    //Wechat Valid
    private function valid()
    {
        $echoStr = $_GET["echostr"];

        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
}