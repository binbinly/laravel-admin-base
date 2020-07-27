<?php

use Encore\Admin\Controllers\AuthController;
use Illuminate\Routing\Router;

Route::group([
    'prefix' => config('admin.route.prefix'),
    'middleware' => config('admin.route.middleware'),
], function ($router) {
    $router->namespace('\AdminBase\Controllers')->group(function (Router $router) {
        //----------------------------二次登陆验证----------------------------//
        $router->put('auth/setting/enable_2fa', 'Auth\SecurityController@validateTwoFactor')->name('开启二次验证');
        $router->put('auth/setting/disable_2fa', 'Auth\SecurityController@deactivateTwoFactor')->name('关闭二次验证');
        $router->post('auth/validate2fa', 'Auth\Validate2faController@index')->name('二次登陆验证');

        //----------------------------项目导航----------------------------//
        $router->resource('nav/categories', 'WebStack\CategoryController');
        $router->resource('nav/sites', 'WebStack\SiteController');

        //----------------------------自定义laravel-admin核心路由----------------------------//
        $router->resource('auth/logs', 'Admin\LogController')->names('用户日志');
        $router->resource('auth/users', 'Admin\UserController')->names('用户管理');
        $router->resource('auth/permissions', 'Admin\PermissionController')->names('权限管理');
        $router->resource('auth/roles', 'Admin\RoleController')->names('角色管理');
    });
    $authController = config('admin.auth.controller', AuthController::class);
    $router->put('auth/setting/password', $authController.'@password')->name('修改密码');
});