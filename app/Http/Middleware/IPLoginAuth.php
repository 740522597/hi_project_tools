<?php

namespace App\Http\Middleware;

use App\Models\IPLoginUser;
use App\Jobs\IPLoginRegisterJob;
use App\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class IPLoginAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ip = $request->getClientIp();
        $username = $request->get('username', null);

        $ipLogin = IPLoginUser::query()
            ->with('user')
            ->where('ip', $ip)
            ->where('login_status', 1)
            ->first();

        if ($ipLogin) {
            $ipLogin->last_request_at = Carbon::now();
            $ipLogin->save();
            Auth::loginUsingId($ipLogin->user->id);
            return $next($request);
        }

//        if (env('APP_ENV') == 'local') {
//            return $next($request);
//        }
        if (!$username) {
            return response()->json(['success' => false, 'message' => '你没有权限操作.', 'status' => 403]);
        }

        $user = User::query()
            ->where('name', $username)
            ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => '用户不存在.', 'status' => 403]);
        }

        $ipLogin = IPLoginUser::query()
            ->with('user')
            ->where('user_id', $user->id)
            ->first();

        if (!$ipLogin) {
            return response()->json(['success' => false, 'message' => '非法登录.', 'status' => 403]);
        }
        if ($ipLogin->ip == $ip && $ipLogin->login_status == true) {
            $ipLogin->last_request_at = Carbon::now();
            $ipLogin->save();
            Auth::loginUsingId($user->id);
            return $next($request);
        }

        if (Carbon::parse($ipLogin->last_request_at)->addMinutes(5)->lt(Carbon::now())) {
            $ipLogin->last_request_at = Carbon::now();
            $ipLogin->save();
            IPLoginRegisterJob::dispatch($ipLogin, $ip);
        }
        return response()->json(['success' => true, 'message' => '请在微信端确认登录', 'status' => 403]);

    }
}
