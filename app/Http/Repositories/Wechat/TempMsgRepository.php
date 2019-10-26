<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-10-27
 * Time: 01:10
 */
namespace App\Http\Repositories\Wechat;

use App\Http\Repositories\Wechat\WechatBaseRepository;

class TempMsgRepository extends WechatBaseRepository
{
    public function sendMerchAPIMonitor($data)
    {
        $openid = 'o3KgR1C81_5tGuO0ml2gSWrPs8SI';

        $color = 'green';

        if ($data['status'] == 'FAILED') {
            $color = 'red';
        }

        $tempMsg = '{
                   "touser":"'.$openid.'",
                   "template_id":"'.env("API_MONITOR_TEMP_ID").'",
                    "data":{
                        "request_time":{
                            "value":"'.$data['request_time'].'"
                        },
                        "text":{
                            "value":"'.$data['text'].'",
                            "color":"'.$color.'"
                        }
                    }
               }';
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->accessToken;
        $this->https_request($url, $tempMsg);
    }
}