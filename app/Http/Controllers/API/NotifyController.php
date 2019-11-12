<?php
/**
 * Created by PhpStorm.
 * User: Haiyang
 * Date: 2019-11-12
 * Time: 22:34
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Repositories\NotifyRepository;
use Illuminate\Http\Request;

class NotifyController extends Controller
{
    protected $repo = null;

    public function __construct()
    {
        $this->repo = new NotifyRepository();
    }

    public function notify(Request $request)
    {
        try {
            $notify = $this->repo->gatherNotify();
            return response()->json(['success' => true, 'notify' => $notify]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}