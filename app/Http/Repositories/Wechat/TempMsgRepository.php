<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-10-27
 * Time: 01:10
 */
namespace App\Http\Repositories\Wechat;

use App\Http\Repositories\Wechat\WechatBaseRepository;
use App\Jobs\PushWechatTempMsg;
use Carbon\Carbon;

class TempMsgRepository extends WechatBaseRepository
{
    public function sendMerchAPIMonitor($data)
    {
        $openid = 'o3KgR1C81_5tGuO0ml2gSWrPs8SI';
        $color = 'green';
        if ($data['status'] != 200) {
            $color = 'red';
        }

        $tempMsg = '{
                   "touser":"'.$openid.'",
                   "template_id":"'.env("API_MONITOR_TEMP_ID").'",
                    "data":{
                        "request_time":{
                            "value":"'.$data['request_time'].'"
                        },
                        "status":{
                            "value":"'.$data['status'].'",
                            "color":"'.$color.'"
                        }
                    }
               }';
        PushWechatTempMsg::dispatch($tempMsg);
    }

    public function sendIPLoginMsg($ipLogin, $ip)
    {
        $color = 'green';
        $tempMsg = '{
                   "touser":"'.$ipLogin->wechat_open_id.'",
                   "template_id":"'.env("IPLOGIN_TEMP_ID").'",
                    "data":{
                        "name":{
                            "value":"'.$ipLogin->user->name.'",
                            "color":"'.$color.'"
                        },
                        "time":{
                            "value":"'.Carbon::now().'",
                            "color":"'.$color.'"
                        },
                        "ip":{
                            "value":"'.$ip.'",
                            "color":"'.$color.'"
                        }
                    }
               }';
        PushWechatTempMsg::dispatch($tempMsg);
    }
}