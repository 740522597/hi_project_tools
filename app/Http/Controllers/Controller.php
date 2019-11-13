<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\QueryTrait;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, QueryTrait;

    public function __construct()
    {
    }

    public function success($results = [])
    {
        $data = ['success' => true];
        foreach ($results as $key => $result) {
            $data[$key] = $result;
        }
        return response()->json($data);
    }

    public function failed($message)
    {
        return response()->json(['success' => false, 'message' => $message]);
    }
}
