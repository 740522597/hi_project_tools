<?php

namespace App\Console\Commands;

use App\Http\Repositories\Wechat\TempMsgRepository;
use App\Jobs\PushWechatTempMsg;
use App\Models\HPTask;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PushTaskDueAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push-due-tasks';

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
        $tasks = HPTask::query()
            ->with('plan.project')
            ->where('due_at', '<', Carbon::now())
            ->where('status', '<>', HPTask::TASK_STATUS_DONE)
            ->get();

        foreach ($tasks as $task) {
            if (($task->notified_at && Carbon::now()->addMinutes(-10)->gt(Carbon::parse($task->notified_at)))
                || !$task->notified_at) {
                $openid = 'o3KgR1C81_5tGuO0ml2gSWrPs8SI';
                $tempMsg = '{
                   "touser":"'.$openid.'",
                   "template_id":"'.env("TASK_DUE_TEMP_ID").'",
                    "data":{
                        "project":{
                            "value":"'.$task->plan->project->name.'"
                        },
                        "plan":{
                            "value":"'.$task->plan->title.'"
                        },
                        "task":{
                            "value":"'.$task->title.'"
                        },
                        "due_at":{
                            "value":"'.$task->due_at.'",
                            "color": "red"
                        }
                    }
               }';
                PushWechatTempMsg::dispatch($tempMsg);
                $task->notified_at = Carbon::now();
                $task->save();
            }
        }
    }
}
