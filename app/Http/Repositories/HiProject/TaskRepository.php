<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-11-12
 * Time: 14:02
 */

namespace App\Http\Repositories\HiProject;


use App\Models\HPPlan;
use App\Models\HPTask;
use Carbon\Carbon;

class TaskRepository
{
    /**
     * @param $projectId
     * @param $goalType
     * @return mixed
     * @throws \Exception
     */
    public function getGoalTasks($projectId, $goalType)
    {
        if (!$projectId || !$goalType) {
            throw new \Exception('缺少参数.');
        }
        $timeFrom = null;
        $timeTo = null;
        if ($goalType == 'MONTH') {
            $timeFrom = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
            $timeTo = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
        }
        if ($goalType === 'WEEK') {
            $timeFrom = Carbon::now()->startOfWeek()->format('Y-m-d H:i:s');
            $timeTo = Carbon::now()->endOfWeek()->format('Y-m-d H:i:s');
        }

        if (!$timeTo || !$timeFrom) {
            throw new \Exception('任务追踪目标不正确.');
        }

        $planIds = HPPlan::query()
            ->where('project_id', $projectId)
            ->pluck('id');

        $tasks = HPTask::query()
            ->with('plan', 'sub_tasks')
            ->whereIn('plan_id', $planIds)
            ->where('due_at', '>', $timeFrom)
            ->where('due_at', '<', $timeTo)
            ->orderBy('due_at', 'asc')
            ->get();

        return $tasks;
    }
}