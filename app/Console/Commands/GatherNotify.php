<?php

namespace App\Console\Commands;

use App\Models\HPTask;
use App\Notify;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GatherNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:gather';

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
            ->where('due_at', '<', Carbon::now())
            ->get();
        foreach ($tasks as $task) {
            $notify = Notify::query()
                ->where('type', Notify::TYPE_TASK_DUE)
                ->where('hash', $task->id)
                ->first();
            if (!$notify) {
                Notify::query()->create([
                    'type' => Notify::TYPE_TASK_DUE,
                    'hash' => $task->id
                ]);
            }
        }
    }
}
