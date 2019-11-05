<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-10-27
 * Time: 00:32
 */
namespace App\Http\Repositories\Wechat;

use App\Models\AccessToken;
use Carbon\Carbon;

class WechatBaseRepository {
    private $appID = null;
    private $appSecret = null;
    protected $accessToken = null;

    public function __construct()
    {
        $this->appID = env("WECHAT_APPID");
        $this->appSecret = env("WECHAT_APPSECRET");
        $this->accessToken = $this->getAccessToken();
    }

    public function getAccessToken()
    {
        $token = AccessToken::query()
            ->firstOrCreate([
                'type' => AccessToken::TYPE_WECHAT
            ]);
        $expired_at = Carbon::parse('+1 hours');
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

    public function downloadMedia($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package=curl_exec($ch);
        curl_close($ch);
        return $package;
    }

    public function saveMedia($mediaId, $path){
        $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $this->getAccessToken() . '&media_id=' . $mediaId;
        $content = $this->downloadMedia($url);
        $local_file=fopen($path,'w');
        if(false!==$local_file){
            if(false!==fwrite($local_file, $content)){
                fclose($local_file);
            }
        }
    }
}