<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-10-27
 * Time: 15:07
 */

namespace App\Http\Controllers\Wechat;


use App\Http\Repositories\OCR\OcrRepository;
use App\Http\Repositories\Wechat\MsgRepository;
use App\Http\Repositories\Wechat\WechatBaseRepository;
use App\WechatUserMsg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WechatController
{
    public function msgReceiver()
    {
        $msgRepo = new MsgRepository();

        $xmlStr = file_get_contents("php://input");    //php7.0只能用这种方式获取数据，之前的$GLOBALS['HTTP_RAW_POST_DATA']7.0版本不可用
        Log::info($xmlStr);
        $postObj = simplexml_load_string($xmlStr);    //读取xml格式文件,记得安装php7.0-xml
        $reply = null;

        if (strtolower($postObj->MsgType) == 'event') {
            if ($response = $msgRepo->receiveTypeEvent($postObj)) {
                $reply = $response;
            }
        }


        if (strtolower($postObj->MsgType) == 'text') {
            if ($response = $msgRepo->receiveTypeText($postObj)) {
                $reply = $response;
            }
        }

        if (strtolower($postObj->MsgType) == 'image') {
            $msgRepo->receiveTypeImage($postObj);
        }

        if ($reply) {
            echo $reply;
            exit;
        }
        echo '';
        exit;
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