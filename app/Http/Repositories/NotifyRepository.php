<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-11-12
 * Time: 22:35
 */

namespace App\Http\Repositories;


use App\Models\HPTask;
use App\Notify;
use Carbon\Carbon;

class NotifyRepository
{
    public $notify = [];
    public function __construct()
    {
        $notifies = [
          'TASK_DUE'
        ];
    }

    public function gatherNotify()
    {
        $notifies = Notify::query()
            ->where('type', Notify::TYPE_TASK_DUE)
            ->whereNull('notified_at')
            ->get();
        if (count($notifies) <= 0) {
            return null;
        }
        foreach ($notifies as $notify) {
            $notify->notified_at = Carbon::now();
            $notify->save();
        }

        $this->notify = [
            'title' => Notify::NOTIFY_TITLE[Notify::TYPE_TASK_DUE],
            'body' => '有 ' . count($notifies) . ' 条任务到期，请及时处理!'
        ];

        return $this->notify;
    }
}