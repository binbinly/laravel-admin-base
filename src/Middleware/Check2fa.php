<?php

namespace AdminBase\Middleware;

use AdminBase\Models\Admin\User;
use Closure;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Support\Authenticator;

/**
 * 是否强制开启2fa验证
 * Class Check2Fa
 * @package AdminBase\Middleware
 */
class Check2Fa
{
    /**
     * 强制开启二次登录验证
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!config('base.google2fa_force')) {
            return $next($request);
        }
        $authenticator = app(Authenticator::class)->boot($request);

        if ($authenticator->isAuthenticated()) {
            return $next($request);
        }
        return admin_url('auth/setting');
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
