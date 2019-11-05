<?php

namespace App\Console\Commands;

use App\Models\IPLoginUser;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DestroyIPLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ip-login:destroy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $ipLogins = IPLoginUser::query()
            ->where('login_status', true)
            ->get();
        foreach ($ipLogins as $ipLogin) {
            $destroyAt = Carbon::createFromDate($ipLogin->last_request_at)->addMinutes(env('LOGIN_DESTROY_IN'));
            if (Carbon::now()->gt($destroyAt)) {
                $ipLogin->login_status = false;
                $ipLogin->save();
            }
        }
    }
}
