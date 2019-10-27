<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-10-27
 * Time: 15:07
 */

namespace App\Http\Controllers\Wechat;


use App\Http\Repositories\Wechat\WechatBaseRepository;
use Illuminate\Http\Request;

class WechatController
{
    public function msgReceiver(Request $request)
    {
        $xml_str = $GLOBALS['HTTP_RAW_POST_DATA'];
        \Log::info($xml_str);
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