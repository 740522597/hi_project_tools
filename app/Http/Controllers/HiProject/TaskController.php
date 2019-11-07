<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-11-04
 * Time: 23:05
 */

namespace App\Http\Controllers\HiProject;

use App\Models\HPPlan;
use App\Models\HPProject;
use App\Models\HPTask;
use App\TaskComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function addTask(Request $request)
    {
        try {
            $title = $request->get('title', null);
            $planId = $request->get('plan_id', null);
            $description = $request->get('description', null);
            $dueAt = $request->get('due_at', null);

            if (!$title || !$planId || !$description) {
                throw new \Exception('请检查必填内容.');
            }
            $plan = HPPlan::query()
                ->find($planId);

            if (!$plan) {
                throw new \Exception('目标计划不存在.');
            }
            $task = HPTask::query()
                ->firstOrCreate([
                    'title'       => $title,
                    'description' => $description,
                    'plan_id'     => $planId,
                    'code'        => 0,
                    'prefix'      => 'TASK'
                ]);
            $task->code = $task->id;
            $task->due_at = $dueAt;

            $project = HPProject::query()
                ->where('id', $plan->project_id)
                ->first();
            if (!$project) {
                throw new \Exception('目标项目不存在.');
            }
            $task->prefix = $project->prefix;
            $task->save();

            $tasks = HPTask::query()
                ->where('plan_id', $planId)
                ->orderBy('id', 'desc')
                ->get();
            return response()->json(['success' => true, 'tasks' => $tasks]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function taskList(Request $request)
    {
        try {
            $planId = $request->get('plan_id', null);
            if (!$planId) {
                throw new \Exception('缺少计划ID.');
            }
            $plan = HPPlan::query()
                ->where('id', $planId)
                ->first();
            if (!$plan) {
                throw new \Exception('未能找到该计划');
            }

            $tasks = HPTask::query()
                ->where('plan_id', $plan->id)
                ->orderBy('urgency_level', 'asc')
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
            $task = HPTask::query()
                ->find($taskId);
            if (!$task) {
                throw new \Exception('未能找到对应的任务');
            }
            return response()->json(['success' => true, 'task' => $task]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateTask(Request $request)
    {
        try {
            $taskId = $request->get('task_id', null);
            $title = $request->get('title', null);
            $description = $request->get('description', null);
            $status = $request->get('status', null);
            $dueAt = $request->get('due_at', null);
            if (!$taskId || !$title || !$description) {
                throw new \Exception('缺少必填项.');
            }
            $task = HPTask::query()
                ->find($taskId);
            if (!$task) {
                throw new \Exception('未能找到对应的任务');
            }
            $task->title = $title;
            $task->due_at = $dueAt;
            $task->description = $description;
            if ($status) {
                $task->status = $status;
            }
            $task->save();

            return response()->json(['success' => true, 'task' => $task]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateTaskLevel(Request $request)
    {
        try {
            $levels = $request->all();
            foreach ($levels as $taskId => $level) {
                $task = HPTask::query()
                    ->find($taskId);
                if (!$task) {
                    throw new \Exception('未找到对应任务.');
                }
                $task->urgency_level = $level + 1;
                $task->save();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteTask(Request $request)
    {
        try {
            $taskId = $request->get('task_id', null);
            if (!$taskId) {
                throw new \Exception('缺少任务ID.');
            }
            $task = HPTask::query()
                ->find($taskId);
            if (!$task) {
                throw new \Exception('该任务已被删除.');
            }
            $task->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function taskStatus(Request $request)
    {
        try {
            $taskId = $request->get('id', null);
            $status = $request->get('status', null);
            if (!$taskId || !$status) {
                throw new \Exception('缺少任务ID或状态.');
            }
            $task = HPTask::query()
                ->find($taskId);
            if (!$task) {
                throw new \Exception('该任务已被删除.');
            }
            $task->status = $status;
            $task->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function addTaskComment(Request $request)
    {
        try {
            $taskId = $request->get('task_id', null);
            $content = $request->get('content', null);
            if (!$taskId || !$content) {
                throw new \Exception('缺少任务ID或内容.');
            }
            $task = HPTask::query()
                ->find($taskId);
            if (!$task) {
                throw new \Exception('该任务已被删除.');
            }
            TaskComment::query()
                ->firstOrCreate([
                    'task_id' => $task->id,
                    'content' => $content
                ]);
            $comments = TaskComment::query()
                ->where('task_id', $task->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'comments' => $comments]);
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
            $task = HPTask::query()
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

    public function deleteComment(Request $request)
    {
        try {
            $commentId = $request->get('comment_id', null);
            if (!$commentId) {
                throw new \Exception('缺少评论ID.');
            }
            $comment = TaskComment::query()
                ->find($commentId);
            if (!$comment) {
                throw new \Exception('该评论已被删除.');
            }
            $taskId = $comment->task_id;
            $comment->delete();
            $comments = TaskComment::query()
                ->where('task_id', $taskId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'comments' => $comments]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}