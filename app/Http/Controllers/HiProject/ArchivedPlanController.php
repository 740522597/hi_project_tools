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

class ArchivedPlanController extends Controller
{
    public function planList(Request $request)
    {
        try {
            $projectId = $request->get('project_id', null);
            if (!$projectId) {
                throw new \Exception('缺少项目ID.');
            }
            $project = $this->myQuery(HPProject::query())
                ->where('id', $projectId)
                ->first();
            if (!$project) {
                throw new \Exception('未能找到该项目');
            }

            $plans = $this->myQuery(ArchivedPlan::query())
                ->where('project_id', $project->id)
                ->orderBy('id', 'desc')
                ->get();

            return response()->json(['success' => true, 'plans' => $plans]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}