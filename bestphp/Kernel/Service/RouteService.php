<?php


namespace Best\Kernel\Service;


use Best\Route\Dispatcher\Dispatcher;
use Best\Route\Router;
use Best\Kernel\Service\Contract\Service;

class RouteService extends Service
{
    /**
     * Run the service
     *
     * @return mixed
     */
    public function run()
    {
        $this->app->bind([
            'router'      => Router::class,
            'dispatcher'  => Dispatcher::class
        ]);
        $this->app['router']->load($this->app->getRoutePath());
    }
}