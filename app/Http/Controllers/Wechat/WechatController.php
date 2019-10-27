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
        $wechatRepository = new WechatBaseRepository();
        $wechatRepository->valid();
    }


}