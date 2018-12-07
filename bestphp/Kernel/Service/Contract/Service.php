<?php


namespace Best\Kernel\Service\Contract;


use Best\Kernel\App;

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
     * Run the service
     *
     * @return mixed
     */
    abstract public function run();
}