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
            $projects = HPProject::query()
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'projects' => $projects]);
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