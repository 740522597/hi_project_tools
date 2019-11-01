<?php

namespace App\Http\Middleware;

use App\IPLoginUser;
use App\Jobs\IPLoginRegisterJob;
use App\User;
use Carbon\Carbon;
use Closure;

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

        if (!$username) {
            abort(403, '您没有权限操作');
        }

        $user = User::query()
            ->where('name', $username)
            ->first();

        if (!$user) {
            abort(403, '用户不存在');
        }

        $ipLogin = IPLoginUser::query()
            ->with('user')
            ->where('user_id', $user->id)
            ->first();

        if (!$ipLogin) {
            abort(403, '非法登录');
        }
        $ipLogin->last_request_at = Carbon::now();
        $ipLogin->save();
        if ($ipLogin->ip == $ip && $ipLogin->login_status == true) {
            return $next($request);
        }
        IPLoginRegisterJob::dispatch($ipLogin, $ip);

        abort(403, '请在微信中确认登陆');
    }
}
