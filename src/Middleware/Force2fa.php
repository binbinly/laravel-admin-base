<?php

namespace AdminBase\Middleware;

use Closure;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;

/**
 * 是否强制开启2fa
 * Class Force2fa
 * @package AdminBase\Middleware
 */
class Force2fa
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
        if (Admin::user() && Admin::user()->google2fa_secret != '') {
            return $next($request);
        }
        if ($this->shouldPassThrough($request)) {
            return $next($request);
        }
        $force2Fa = false;
        if (Admin::user() && Admin::user()->isAdministrator()) {
            $force2Fa = true;
        }
        foreach (Admin::user()->roles as $role) {
            if ($role->is_2fa == 1) {
                $force2Fa = true;
                break;
            }
        }
        if ($force2Fa) {
            admin_error('亲，强制二次登陆验证已开启，请在安全栏开启吧，开启后才能正常操作哦!!!!!!!');
            return redirect()->guest('auth/setting');
        }
        return $next($request);
    }

    /**
     * 过滤路由
     * @param $request
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        $excepts = [
            'auth/login',
            'auth/logout',
            'auth/recovery',
            'auth/check',
            'auth/verify',
            'auth/setting/enable_2fa',
            'auth/setting/disable_2fa',
            'auth/validate2fa',
            'auth/setting'
        ];

        return collect($excepts)
            ->map('admin_base_path')
            ->contains(function ($except) use ($request) {
                if ($except !== '/') {
                    $except = trim($except, '/');
                }

                return $request->is($except);
            });
    }
}
