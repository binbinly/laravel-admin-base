<?php


namespace AdminBase;


use Encore\Admin\Extension;

class AdminBase extends Extension
{
    public $name = 'base';

    public $views = __DIR__.'/../resources/views';
    
    public static function router(){
        //需要限流的路由，每日100次
        app('router')->group([
            'prefix' => config('admin.route.prefix'),
            'middleware' => ['web', 'admin', 'throttle:100,1440']
        ], function ($router) {
            $router->namespace('\AdminBase\Controllers')->group(function ($router) {
                $router->get('auth/recovery', 'Auth\RecoveryLoginController@get')->name('恢复代码登录页面');
                $router->post('auth/recovery', 'Auth\RecoveryLoginController@store')->name('恢复代码登录');
            });

            $authController = config('admin.auth.controller', AuthController::class);
            $router->get('auth/check', $authController.'@check')->name('验证码验证');
            $router->get('auth/verify', $authController.'@verify')->name('验证码生成');
        });
    }
}