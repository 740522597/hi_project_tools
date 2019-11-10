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
use App\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
                    'title'         => $title,
                    'description'   => $description,
                    'plan_id'       => $planId,
                    'code'          => 0,
                    'prefix'        => 'TASK',
                    'urgency_level' => -1
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
                ->orderBy('urgency_level', 'asc')
                ->get();

            foreach ($tasks as $key => $task) {
                $task->urgency_level = $key;
                $task->save();
            }

            $tasks = HPTask::query()
                ->where('plan_id', $planId)
                ->orderBy('urgency_level', 'asc')
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

    public function loadGoalTasks(Request $request)
    {
        try {
            $projectId = $request->get('project_id', null);
            $status = $request->get('status', null);
            $goalType = $request->get('goal_type', null);
            if (!$projectId || !$status || !$goalType) {
                throw new \Exception('缺少参数.');
            }
            $time = null;
            if ($goalType == 'MONTH') {
                $time = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');
            }
            if ($goalType === 'WEEK') {
                $time = Carbon::now()->endOfWeek()->format('Y-m-d H:i:s');
            }

            if (!$time) {
                throw new \Exception('任务追踪目标不正确.');
            }

            $planIds = HPPlan::query()
                ->where('project_id', $projectId)
                ->pluck('id');

            $tasks = HPTask::query()
                ->whereIn('plan_id', $planIds)
                ->where('due_at', '<', $time)
                ->where('status', $status)
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
            $taskIds = [];
            foreach ($levels as $taskId => $level) {
                $task = HPTask::query()
                    ->find($taskId);
                if (!$task) {
                    throw new \Exception('未找到对应任务.');
                }
                $taskIds[] = $taskId;
                $task->urgency_level = $level + 1;
                $task->save();
            }

            $tasks = HPTask::query()
                ->whereIn('id', $taskIds)
                ->orderBy('urgency_level', 'asc')
                ->get();

            foreach ($tasks as $key => $task) {
                $task->urgency_level = $key;
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

            $planId = $task->planId;
            $task->delete();

            $tasks = HPTask::query()
                ->where('plan_id', $planId)
                ->orderBy('urgency_level', 'asc')
                ->get();

            foreach ($tasks as $key => $task) {
                $task->urgency_level = $key;
                $task->save();
            }

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

    public function uploadFile(Request $request)
    {
        try {
            $data = $request->all();
            $task = HPTask::query()
                ->find($data['task_id']);
            if (!$task) {
                throw new \Exception('任务不存在.');
            }
            $file = $request->file('file');
            if (!$file) {
                throw new \Exception('文件上传失败');
            }
            if ($file->isValid()) {
                $fileExtension = $file->getClientOriginalExtension();
                if (!in_array($fileExtension, ['png', 'jpg', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'pdf'])) {
                    throw new \Exception('文件格式不正确');
                }
                $tmpFile = $file->getRealPath();
                $realName = $file->getClientOriginalName();
                if (filesize($tmpFile) >= 1024000) {
                    throw new \Exception('文件大小超过限制');
                }
                if (!is_uploaded_file($tmpFile)) {
                    throw new \Exception('非法上传');
                }
                $fileName = date('Y_m_d') . '/' . md5(time()) . mt_rand(0, 9999) . '.' . $fileExtension;
                if (Storage::disk('public')->put($fileName, file_get_contents($tmpFile))) {
                    $path = 'app/public/' . $fileName;
                    Upload::query()
                        ->firstOrCreate([
                            'task_id' => $task->id,
                            'name'    => $realName,
                            'path'    => $path
                        ]);
                }
            }
            $files = Upload::query()
                ->where('task_id', $task->id)
                ->get();
            return response()->json(['success' => true, 'files' => $files]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getFiles(Request $request)
    {
        try {
            $data = $request->all();
            $task = HPTask::query()
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

    public function deleteFile(Request $request)
    {
        try {
            $data = $request->all();
            $file = Upload::query()
                ->find($data['file_id']);
            if (!$file) {
                throw new \Exception('文件不存在.');
            }
            if (file_exists(storage_path($file->path))) {
                unlink(storage_path($file->path));
            }
            $taskId = $file->task_id;
            $file->delete();
            $files = Upload::query()
                ->where('task_id', $taskId)
                ->get();
            return response()->json(['success' => true, 'files' => $files]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}