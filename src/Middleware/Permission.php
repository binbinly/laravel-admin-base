<?php

namespace AdminBase\Middleware;


use Encore\Admin\Auth\Permission as Checker;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;

/**
 * 权限验证
 * Class NewPermission
 * @package AdminBase\Middleware
 */
class Permission extends \Encore\Admin\Middleware\Permission
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @param array $args
     *
     * @return mixed
     */
    public function handle(Request $request, \Closure $next, ...$args)
    {
        if (config('admin.check_route_permission') === false) {
            return $next($request);
        }

        if (!Admin::user() || !empty($args) || $this->shouldPassThrough($request)) {
            return $next($request);
        }

        if ($this->checkRoutePermission($request)) {
            return $next($request);
        }

        //接口跳过权限认证
        if($request->route()->getPrefix() == '/api') {
            return $next($request);
        }

        if (!Admin::user()->allPermissions()->where('http_path', '<>', '')->first(function ($permission) use ($request) {
            return $permission->shouldPassThrough($request);
        })) {
            Checker::error();
        }

        return $next($request);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        $excepts = array_merge(config('admin.auth.excepts', []), [
            'auth/login',
            'auth/logout',
            '_handle_action_',
            '_handle_form_',
            '_handle_selectable_',
            '_handle_renderable_',
            'auth/recovery',
            'auth/check',
            'auth/verify',
            'auth/setting/enable_2fa',
            'auth/setting/disable_2fa',
            'auth/validate2fa',
            'auth/setting'
        ]);

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
