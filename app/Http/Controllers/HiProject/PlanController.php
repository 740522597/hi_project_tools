<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-11-04
 * Time: 23:05
 */

namespace App\Http\Controllers\HiProject;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlanController extends Controller
{
    public function addPlan(Request $request)
    {
        try {
            return response()->json($request);
        } catch (\Exception $e) {
            return $e;
        }
    }
}