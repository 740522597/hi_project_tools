<?php

namespace App\Jobs;

use App\Models\IPLoginUser;
use App\Models\WechatUserMsg;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class IPLoginSetTrue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $openId = null;

    /**
     * Create a new job instance.
     *
     * @param $openId
     */
    public function __construct($openId)
    {
        $this->openId = $openId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        WechatUserMsg::query()
            ->where('to_user', $this->openId)
            ->where('msg_type', WechatUserMsg::IP_LOGIN_TYPE)
            ->orderBy('id', 'desc')
            ->first();
        $ipLogin = IPLoginUser::query()
            ->where('wechat_open_id', $this->openId)
            ->first();
        $ipLogin->login_status = true;
        $ipLogin->last_request_at = Carbon::now();
        $ipLogin->save();
    }
}
