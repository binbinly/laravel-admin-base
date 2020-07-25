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
        'admin.auth' => \Encore\Admin\Middleware\Authenticate::class,
        'admin.pjax' => \Encore\Admin\Middleware\Pjax::class,
        'admin.bootstrap' => \Encore\Admin\Middleware\Bootstrap::class,
        'admin.session' => \Encore\Admin\Middleware\Session::class,
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
            'admin.bootstrap',
            'admin.permission',
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

        //go-fastdfs适配器
        Storage::extend('dfs', function ($app, $config) {
            $adapter = new FastDFSAdapter($config['root'], $config['api']);
            return new Filesystem($adapter);
        });
    }
}