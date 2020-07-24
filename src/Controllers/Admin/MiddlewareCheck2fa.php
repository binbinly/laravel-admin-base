<?php

namespace AdminBase\Middleware;

use AdminBase\Models\Admin\NewOperationLog;
use AdminBase\Models\Admin\User;
use Closure;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use ReflectionException;
use ReflectionClass;
use Exception;

class MiddlewareCheck2fa
{
    /**
     * 二次登录验证
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //检查是否开启了二次验证
        $user = Admin::user();
        if (strstr($request->getRequestUri(), '/auth/setting')) {
            return $next($request);
        }
        if ($user && Admin::user()->isAdministrator()) {
            return $next($request);
        }
        //如果未开启，路由正常走
        if ($user && $user->is_validate == User::IS_CALIDATE_OFF) {
            return $next($request);
        }

        //如果开启，但是google2fa_secret和recovery_code 为null，也需要重定向
        if ($user && $user->is_validate == User::IS_CALIDATE_ON && empty($user->google2fa_secret) && empty($user->recovery_code)) {
            return redirect('/auth/setting');
        }
        return $next($request);
    }
}
