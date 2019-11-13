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

class ProjectController extends Controller
{
    public function loadProjects(Request $request)
    {
        try {
            $projects = $this->myQuery(HPProject::query())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'projects' => $projects]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function addProject(Request $request)
    {
        try {
            $name = $request->get('name', null);
            $prefix = $request->get('prefix', null);
            $description = $request->get('description', null);
            if (!$name || !$prefix) {
                throw new \Exception('缺少参数');
            }
            $project = $this->myQuery(HPProject::query())
                ->firstOrCreate([
                   'prefix' => $prefix,
                   'name' => $name,
                   'created_by' => auth()->user()->id,
                   'description' => $description
                ]);

            return response()->json(['success' => true, 'project' => $project]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

//    public function demo(Request $request)
//    {
//        try {
//
//            return response()->json(['success' => true]);
//        } catch (\Exception $e) {
//            return response()->json(['success' => false, 'message' => $e->getMessage()]);
//        }
//    }
}