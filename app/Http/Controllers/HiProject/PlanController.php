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
            $name = $request->get('plan_name', null);
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
            HPPlan::query()
                ->firstOrCreate([
                   'project_id' => $project->id,
                   'title' => $name
                ]);

            $plans = HPPlan::query()
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
                ->orderBy('id', 'desc')
                ->get();

            return response()->json(['success' => true, 'plans' => $plans]);
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
                ->orderBy('id', 'desc')
                ->get();

            return response()->json(['success' => true, 'tasks' => $tasks]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}