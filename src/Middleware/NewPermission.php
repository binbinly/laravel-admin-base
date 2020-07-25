<?php

namespace AdminBase\Middleware;


use Encore\Admin\Auth\Permission as Checker;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Middleware\Permission;
use Illuminate\Http\Request;

/**
 * 权限验证
 * Class NewPermission
 * @package AdminBase\Middleware
 */
class NewPermission extends Permission
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

        if (in_array($request->route()->getActionName(), [
            'AdminBase\Controllers\Auth\SecurityController@validateTwoFactor',
            'AdminBase\Controllers\Auth\SecurityController@deactivateTwoFactor',
            'App\Admin\Controllers\AuthController@getSetting',
            'AdminBase\Controllers\Auth\RecoveryLoginController@store',
            'AdminBase\Controllers\Auth\RecoveryLoginController@get',
            'AdminBase\Controllers\Auth\Validate2faController@index'
        ])) {
            return $next($request);
        }
        //接口跳过权限认证
        if($request->route()->getPrefix() == '/api') {
            return $next($request);
        }
        $currPath = $request->path();
        if (in_array($currPath , ['/', '_handle_form_', '_handle_action_'])) {
            return $next($request);
        }

        if (!Admin::user()->allPermissions()->where('http_path', '<>', '')->first(function ($permission) use ($request) {
            return $permission->shouldPassThrough($request);
        })) {
            Checker::error();
        }

        return $next($request);
    }
}
