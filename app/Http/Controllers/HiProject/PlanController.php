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
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlanController extends Controller
{
    public function addPlan(Request $request)
    {
        try {
            $name = $request->get('title', null);
            $id = $request->get('id', null);
            $projectId = $request->get('project_id', null);
            if (!$name || !$projectId) {
                throw new \Exception('缺少计划名称或者项目.');
            }
            $project = $this->myQuery(HPProject::query())
                ->where('id', $projectId)
                ->first();
            if (!$project) {
                throw new \Exception('未能找到该项目');
            }
            if (!$id) {
                $this->myQuery(HPPlan::query())
                    ->firstOrCreate([
                        'project_id' => $project->id,
                        'title' => $name,
                        'urgency_level' => -1,
                        'created_by' => auth()->user()->id
                    ]);
            } else {
                $this->myQuery(HPPlan::query())->where('id', $id)
                    ->update([
                        'title' => $name
                    ]);
            }

            $plans = $this->myQuery(HPPlan::query())
                ->where('project_id', $projectId)
                ->orderBy('urgency_level', 'asc')
                ->get();

            foreach ($plans as $key => $plan) {
                $plan->urgency_level = $key;
                $plan->save();
            }

            $plans = $this->myQuery(HPPlan::query())
                ->where('project_id', $project->id)
                ->orderBy('urgency_level', 'asc')
                ->get();

            return response()->json(['success' => true, 'plans' => $plans]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function planList(Request $request)
    {
        try {
            $projectId = $request->get('project_id', null);
            if (!$projectId) {
                throw new \Exception('缺少项目ID.');
            }
            $projects = $this->myQuery(HPProject::query())->orderBy('id', 'desc')->get();
            $project = $this->myQuery(HPProject::query())
                ->where('id', $projectId)
                ->first();
            if (!$project) {
                throw new \Exception('未能找到该项目');
            }

            $plans = $this->myQuery(HPPlan::query())
                ->where('project_id', $project->id)
                ->orderBy('urgency_level', 'asc')
                ->get();

            return $this->success(['plans' => $plans, 'projects' => $projects]);
        } catch (\Exception $e) {
            return $this->failed($e->getMessage());
        }
    }

    public function updatePlanLevel(Request $request)
    {
        try {
            $levels = $request->all();
            $planIds = [];
            foreach ($levels as $planId => $level) {
                $plan = $this->myQuery(HPPlan::query())
                    ->find($planId);
                if (!$plan) {
                    throw new \Exception('未找到对应计划.');
                }
                $planIds[] = $planId;
                $plan->urgency_level = $level + 1;
                $plan->save();
            }

            $plans = $this->myQuery(HPPlan::query())
                ->whereIn('id', $planIds)
                ->orderBy('urgency_level', 'asc')
                ->get();

            foreach ($plans as $key => $plan) {
                $plan->urgency_level = $key;
                $plan->save();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deletePlan(Request $request)
    {
        try {
            $planId = $request->get('plan_id', null);
            if (!$planId) {
                throw new \Exception('缺少计划ID.');
            }
            $plan = $this->myQuery(HPPlan::query())
                ->find($planId);
            if (!$plan) {
                throw new \Exception('该计划已被删除.');
            }
            $tasksCount = $this->myQuery(HPTask::query())
                ->where('plan_id', $planId)
                ->count();

            if ($tasksCount > 0) {
                throw new \Exception('请先删除计划内任务.');
            }
            $projectId = $plan->project_id;
            $plan->delete();

            $plans = $this->myQuery(HPPlan::query())
                ->where('project_id', $projectId)
                ->orderBy('urgency_level', 'asc')
                ->get();

            foreach ($plans as $key => $plan) {
                $plan->urgency_level = $key;
                $plan->save();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function archivePlan(Request $request)
    {
        try {
            $planId = $request->get('plan_id', null);
            if (!$planId) {
                throw new \Exception('缺少计划ID.');
            }
            $plan = $this->myQuery(HPPlan::query())
                ->find($planId);
            if (!$plan) {
                throw new \Exception('该计划已被删除.');
            }
            $tasksCount = $this->myQuery(HPTask::query())
                ->where('plan_id', $planId)
                ->whereNotIn('status', ['DONE'])
                ->count();

            if ($tasksCount > 0) {
                throw new \Exception('请先完成计划内任务.');
            }
            $planArray = $plan->toArray();
            unset($planArray['unfinished_count']);

            $this->myQuery(ArchivedPlan::query())
                ->firstOrCreate($planArray);

            $tasks = $this->myQuery(HPTask::query())
                ->where('plan_id', $plan->id)
                ->get();

            foreach ($tasks as $task) {
                $taskArray = $task->toArray();
                unset($taskArray['is_pass_due']);
                $this->myQuery(ArchivedTask::query())
                    ->firstOrCreate($taskArray);
                $task->delete();
            }
            $plan->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getPlan(Request $request)
    {
        try {
            $planId = $request->get('plan_id', null);
            if (!$planId) {
                throw new \Exception('缺少计划ID.');
            }
            $plan = $this->myQuery(HPPlan::query())
                ->find($planId);
            if (!$plan) {
                throw new \Exception('该计划已被删除.');
            }

            return response()->json(['success' => true, 'plan' => $plan]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}