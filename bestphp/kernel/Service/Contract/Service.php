<?php


namespace Best\Service\Contract;


use Best\App;

abstract class Service
{
    /**
     * The application instance
     *
     * @var App
     */
    protected $app;
    
    /**
     * ServiceInterface constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Register the service
     *
     * @return mixed
     */
    abstract public function register();

    /**
     * Bootstrap the service
     *
     * @return mixed
     */
    abstract public function boot();
}