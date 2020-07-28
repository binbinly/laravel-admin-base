<?php


namespace AdminBase;


use AdminBase\Adapters\FastDFSAdapter;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Storage;

class AdminBaseServiceProvider extends ServiceProvider
{
    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'admin.log'        => Middleware\LogOperation::class,
        'admin.permission' => Middleware\Permission::class,
        'base.2fa' => \PragmaRX\Google2FALaravel\Middleware::class,
        'throttle' => Middleware\ThrottleRequests::class,
        'base.force2fa' => Middleware\Force2fa::class,
        'base.datetime' => Middleware\DatetimeFormatBefore::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'admin' => [
            'admin.auth',
            'admin.pjax',
            'admin.log',
            'admin.bootstrap',
            'admin.permission',
        ],
        'admin_base' => [
            'base.2fa',
            'base.force2fa',
            'base.datetime'
        ],
    ];

    public function boot(AdminBase $extension)
    {
        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'common');
        }

        //数据迁移
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        //路由
        $this->app->booted(function () {
            AdminBase::routes(__DIR__.'/../routes/web.php');
        });

        //发布配置
        $this->publishes([__DIR__.'/../config/base.php' => config_path('base.php')]);

        //发布静态资源
        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor')],
                'admin-base'
            );
        }

        //go-fastdfs适配器
        Storage::extend('dfs', function ($app, $config) {
            $adapter = new FastDFSAdapter($config['root'], $config['api']);
            return new Filesystem($adapter);
        });
    }

    /**
     * 路由调用时注册事件
     */
    public function register()
    {
        $this->registerRouteMiddleware();
    }

    /**
     * 注册中间件
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }
}