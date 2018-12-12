<?php


namespace Best;


class ServiceManager
{
    /**
     * @var App
     */
    private $app;

    /**
     * The service that has been register
     *
     * @var string
     */
    private $services;

    /**
     * ServiceManager constructor.
     *
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Run the service manager.
     */
    public function run()
    {
        $this->load();
        $this->register();
        $this->boot();
    }

    /**
     * Load the service configuration file.
     */
    public function load()
    {
        $this->services = include $this->app->bootPath() . '/' . 'service.php';
    }

    public function register()
    {
        array_map(function ($service) {
            $service = $this->app->invokeClass($service, [$this->app]);
            $this->app->invoke([$service, 'register']);
        }, $this->services);
    }

    /**
     * Bootstrap the services.
     */
    public function boot()
    {
        array_map(function ($service) {
            $service = $this->app->invokeClass($service, [$this->app]);
            $this->app->invoke([$service, 'boot']);
        }, $this->services);
    }
    
}