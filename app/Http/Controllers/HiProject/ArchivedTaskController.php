<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-11-04
 * Time: 23:05
 */

namespace App\Http\Controllers\HiProject;

use App\ArchivedPlan;
use App\ArchivedTask;
use App\Models\HPPlan;
use App\Models\HPProject;
use App\Models\HPTask;
use App\TaskComment;
use App\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ArchivedTaskController extends Controller
{
    public function taskList(Request $request)
    {
        try {
            $planId = $request->get('plan_id', null);
            if (!$planId) {
                throw new \Exception('缺少计划ID.');
            }
            $plan = ArchivedPlan::query()
                ->where('id', $planId)
                ->first();
            if (!$plan) {
                throw new \Exception('未能找到该计划');
            }

            $tasks = ArchivedTask::query()
                ->where('plan_id', $plan->id)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json(['success' => true, 'tasks' => $tasks]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function taskDetails(Request $request)
    {
        try {
            $taskId = $request->get('task_id', null);
            if (!$taskId) {
                throw new \Exception('缺少任务ID');
            }
            $task = ArchivedTask::query()
                ->find($taskId);
            if (!$task) {
                throw new \Exception('未能找到对应的任务');
            }
            return response()->json(['success' => true, 'task' => $task]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function loadComments(Request $request)
    {
        try {
            $taskId = $request->get('task_id', null);
            if (!$taskId) {
                throw new \Exception('缺少任务ID.');
            }
            $task = ArchivedTask::query()
                ->find($taskId);
            if (!$task) {
                throw new \Exception('该任务已被删除.');
            }
            $comments = TaskComment::query()
                ->where('task_id', $task->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'comments' => $comments]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getFiles(Request $request)
    {
        try {
            $data = $request->all();
            $task = ArchivedTask::query()
                ->find($data['task_id']);
            if (!$task) {
                throw new \Exception('任务不存在.');
            }
            $files = Upload::query()
                ->where('task_id', $task->id)
                ->get();
            return response()->json(['success' => true, 'files' => $files]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}