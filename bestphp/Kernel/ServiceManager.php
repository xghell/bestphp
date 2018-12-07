<?php


namespace Best\Kernel;


use Best\Kernel\Service\Contract\Service;

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
     * @var Service[]
     */
    private $runningServices;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function run()
    {
        $this->boot();
        $this->launchRegisteredServices();
    }

    public function boot()
    {
        $services = include $this->app->getBootPath() . '/service.php';

        $this->register($services);
    }
    
    public function register($service)
    {
        if (is_array($service)) {
            $this->services = $service + ($this->services ?? []);
        } else {
            $this->services[] = $service;
        }
    }
    
    public function unregister($service)
    {
        if ($this->registered($service)) {
            unset($this->services[$service]);
        }
    }
    
    public function registered($service)
    {
        return isset($this->services[$service]);
    }

    public function launchRegisteredServices()
    {
        foreach ($this->services as $key => $value) {
            $service = Container::getInstance()->invokeClass($value, [$this->app]);
            Container::getInstance()->invokeMethod($service, 'run');
            $this->runningServices[$value] = $service;
            unset($this->services[$key]);
        }
    }
}