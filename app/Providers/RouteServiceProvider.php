<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        //

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function map(Router $router)
    {
        $this->mapStaticRoutes($router);
        $this->mapApiRoutes($router);
        $this->mapWebRoutes($router);
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    protected function mapWebRoutes(Router $router)
    {
        $router->group([
            'namespace'  => $this->namespace,
            'middleware' => ['web'],
        ], function ($router) {
            require app_path('Http/Routes/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes does not load session and cookies.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    private function mapApiRoutes($router)
    {
        $router->group([
            'namespace'  => $this->namespace,
            'middleware' => ['api'],
        ], function ($router) {
            require app_path('Http/Routes/api.php');
        });
    }

    /**
     * Define the "static" routes for the application.
     *
     * These routes does not load session and cookies.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    private function mapStaticRoutes($router)
    {
        $router->group([
            'namespace'  => $this->namespace,
            'middleware' => ['static'],
        ], function ($router) {
            require app_path('Http/Routes/static.php');
        });
    }
}
