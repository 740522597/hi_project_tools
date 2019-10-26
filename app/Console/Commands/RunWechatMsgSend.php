<?php

namespace App\Console\Commands;

use App\Http\Repositories\Wechat\MsgRepository;
use App\Http\Repositories\Wechat\TempMsgRepository;
use App\Http\Repositories\WechatBaseRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Ixudra\Curl\Facades\Curl;

class RunMerchAPIMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merch:api-monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Merch API monitor.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tempMsgRepository = new TempMsgRepository();
        $url = env("RIOT_MERCH_URL");
        $data = [
            'request_time' => Carbon::now()
        ];
        $curl = Curl::to($url)
            ->withTimeout(60)
            ->returnResponseObject();
        $response = $curl->post();

        if (!$response) {
            $data['status'] = '0';
        }
        if ($response) {
            $data['status'] = $response->status;
        }
        if ($data['status'] != 200) {
            $tempMsgRepository->sendMerchAPIMonitor($data);
        }
        return;
    }
}
