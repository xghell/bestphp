<?php


namespace Best;


use Best\Facade\Route;

class App extends Container
{
    /**
     * The application version
     */
    const VERSION = '1.0.0';

    /**
     * The root path of application
     *
     * @var string
     */
    private $basePath;

    /**
     * The app path
     *
     * @var string
     */
    private $appPath;

    /**
     * The boot path
     *
     * @var string
     */
    private $bootPath;

    /**
     * The config path
     *
     * @var string
     */
    private $configPath;

    /**
     * The public path
     *
     * @var string
     */
    private $publicPath;

    /**
     * The route path
     *
     * @var string
     */
    private $routePath;

    /**
     * The runtime path
     *
     * @var string
     */
    private $runtimePath;

    /**
     * The vendor path
     *
     * @var string
     */
    private $vendorPath;

    /**
     * The env path
     *
     * @var string
     */
    private $envPath;

    /**
     * The service manager
     *
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * App constructor.
     *
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
        $this->boot();
    }

    /**
     * Bootstrap the app
     */
    public function boot()
    {
        self::setInstance($this);

        $this->launchServiceManger();
    }

    /**
     * Launch service manager
     */
    public function launchServiceManger()
    {
        $this->serviceManager = new ServiceManager($this);
        $this->serviceManager->run();
    }

    /**
     * Handle the http request, Return  the http Response
     */
    public function run()
    {
        //Get http request
        $request = $this['request']->withUri('index/index/I_am_wxg');
        
        $dispatch = Route::dispatch($request);

        $message = $this->invoke([$dispatch, 'execute']);

        $response = $this['response']->withBody($message);

        return $response;
    }

    /**
     * Get base path
     *
     * @return string
     */
    public function basePath()
    {
        return $this->basePath;
    }

    /**
     * Get app path
     *
     * @return string
     */
    public function appPath()
    {
        if (!is_null($this->appPath)) {
            return $this->appPath;
        }
        return $this->appPath = $this->basePath() . '/' . 'app';
    }

    /**
     * @return string
     */
    public function bootPath()
    {
        if (!is_null($this->bootPath)) {
            return $this->bootPath;
        }
        return $this->bootPath = $this->basePath() . '/' . 'boot';
    }

    public function configPath()
    {
        if (!is_null($this->configPath)) {
            return $this->configPath;
        }
        return $this->configPath = $this->basePath() . '/' . 'config';
    }

    public function publicPath()
    {
        if (!is_null($this->publicPath)) {
            return $this->publicPath;
        }
        return $this->publicPath = $this->basePath() . '/' . 'public';
    }

    public function routePath()
    {
        if (!is_null($this->routePath)) {
            return $this->routePath;
        }
        return $this->routePath = $this->basePath() . '/' . 'route';
    }

    public function runtimePath()
    {
        if (!is_null($this->runtimePath)) {
            return $this->runtimePath;
        }
        return $this->runtimePath = $this->basePath() . '/' . 'runtime';
    }

    public function vendorPath()
    {
        if (!is_null($this->vendorPath)) {
            return $this->vendorPath;
        }
        return $this->vendorPath = $this->basePath() . '/' . 'vendor';
    }

    public function envPath()
    {
        if (!is_null($this->envPath)) {
            return $this->envPath;
        }
        return $this->envPath = $this->basePath();
    }
}