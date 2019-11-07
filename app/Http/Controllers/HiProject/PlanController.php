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
            $project = HPProject::query()
                ->where('id', $projectId)
                ->first();
            if (!$project) {
                throw new \Exception('未能找到该项目');
            }
            if (!$id) {
                HPPlan::query()
                    ->firstOrCreate([
                        'project_id' => $project->id,
                        'title' => $name
                    ]);
            } else {
                HPPlan::query()->where('id', $id)
                    ->update([
                        'title' => $name
                    ]);
            }

            $plans = HPPlan::query()
                ->where('project_id', $project->id)
                ->orderBy('id', 'desc')
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
            $project = HPProject::query()
                ->where('id', $projectId)
                ->first();
            if (!$project) {
                throw new \Exception('未能找到该项目');
            }

            $plans = HPPlan::query()
                ->where('project_id', $project->id)
                ->orderBy('urgency_level', 'asc')
                ->get();

            return response()->json(['success' => true, 'plans' => $plans]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updatePlanLevel(Request $request)
    {
        try {
            $levels = $request->all();
            foreach ($levels as $planId => $level) {
                $plan = HPPlan::query()
                    ->find($planId);
                if (!$plan) {
                    throw new \Exception('未找到对应计划.');
                }
                $plan->urgency_level = $level + 1;
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
            $plan = HPPlan::query()
                ->find($planId);
            if (!$plan) {
                throw new \Exception('该计划已被删除.');
            }
            $tasksCount = HPTask::query()
                ->where('plan_id', $planId)
                ->count();

            if ($tasksCount > 0) {
                throw new \Exception('请先删除计划内任务.');
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
            $plan = HPPlan::query()
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