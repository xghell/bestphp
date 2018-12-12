<?php


namespace Best\Service;


use Best\Route\Router;
use Best\Route\Dispatcher;
use Best\Service\Contract\Service;

class RouteService extends Service
{
    /**
     * Run the service
     *
     * @return mixed|void
     */
    public function register()
    {
        $this->app->bind([
            'router'      => Router::class
        ]);
    }
    
    public function boot()
    {
        $routeConfig = $this->app['config']['route'] ?? [];
        $this->app->get('router', [$this->app, $routeConfig]);
        $this->app['router']->load($this->app->routePath());
    }
}