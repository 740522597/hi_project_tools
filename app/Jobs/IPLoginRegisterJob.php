<?php

namespace App\Jobs;

use App\Http\Repositories\Wechat\MsgRepository;
use App\Http\Repositories\Wechat\TempMsgRepository;
use App\Models\WechatUserMsg;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class IPLoginRegisterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ipLogin = null;
    private $ip = null;
    /**
     * Create a new job instance.
     *
     * @param $ipLogin
     * @param $ip
     */
    public function __construct($ipLogin, $ip)
    {
        $this->ip = $ip;
        $this->ipLogin = $ipLogin;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $msgRepo = new TempMsgRepository();
        $msgRepo->sendIPLoginMsg($this->ipLogin, $this->ip);
        $this->ipLogin->ip = $this->ip;
        $this->ipLogin->save();
        WechatUserMsg::query()
            ->firstOrCreate([
                'open_id'     => env('ADMIN_MSG_SEND_FROM'),
                'from_user'   => env('ADMIN_MSG_SEND_FROM'),
                'to_user'     => $this->ipLogin->wechat_open_id,
                'create_time' => Carbon::now(),
                'content'     => WechatUserMsg::IP_LOGIN_TYPE,
                'msg_type'    => WechatUserMsg::IP_LOGIN_TYPE,
                'msg_id'      => null,
                'media_id'    => null,
                'pic_url'     => null
            ]);
    }
}
