<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-10-27
 * Time: 00:32
 */
namespace App\Http\Repositories\Wechat;

use App\AccessToken;
use Carbon\Carbon;

class WechatBaseRepository {
    private $appID = null;
    private $appSecret = null;
    protected $accessToken = null;

    public function __construct($appID = null, $appSecret = null)
    {
        $this->appID = $appID ? $appID : env("WECHAT_APPID");
        $this->appSecret = $appSecret ? $appSecret : env("WECHAT_APPSECRET");
        $this->accessToken = $this->getAccessToken();
    }

    public function getAccessToken()
    {
        $token = AccessToken::query()
            ->firstOrCreate([
                'type' => AccessToken::TYPE_WECHAT
            ]);
        $expired_at = Carbon::parse('+2 hours');
        if (!$token->token || ($token->token && Carbon::parse($token->expired_at)->lt(Carbon::now()))) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appID&secret=$this->appSecret";
            $resp = $this->https_request($url);
            $accessToken = json_decode($resp);
            $token->token = $accessToken->access_token;
            $token->expired_at = $expired_at;
            $token->save();
        }

        return $token->token;
    }

    public function https_request($url,$data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public function getUsers()
    {
        $users = $this->https_request('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->accessToken);
        return json_decode($users, true);
    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];

        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)) {

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>                                                                   
    <ToUserName><![CDATA[%s]]></ToUserName>                                                     
    <FromUserName><![CDATA[%s]]></FromUserName>                                                 
    <CreateTime>%s</CreateTime>                                                                 
    <MsgType><![CDATA[%s]]></MsgType>                                                           
    <Content><![CDATA[%s]]></Content>                                                           
    <FuncFlag>0</FuncFlag>                                                                      
</xml>";

            if (!empty($keyword)) {
                $msgType = "text";
                $contentStr = "Welcome to wechat world!";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            } else {
                echo "Input something...";
            }
        } else {
            echo "";
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