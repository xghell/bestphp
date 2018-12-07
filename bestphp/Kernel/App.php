<?php


namespace Best\Kernel;


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
    }

    public function boot()
    {
        self::setInstance($this);
    }
    
    public function launchServiceManger()
    {
        $this->serviceManager = new ServiceManager($this);
        $this->serviceManager->run();
    }
    
    public function run()
    {
        $this->boot();
        $this->launchServiceManger();
        $this->handle();
    }

    /**
     * Handle the http request, Return  the http Response
     */
    public function handle()
    {
        $request = $this['request']->withUri('index/index/wxg');
        $message = $this['router']->check($request);
        $this['dispatcher']->dispatch($message);
    }
    
    public function getBasePath()
    {
        return $this->basePath;
    }
    
    public function getAppPath()
    {
        if (!is_null($this->appPath)) {
            return $this->appPath;
        }
        return $this->appPath = $this->getBasePath() . '/' . 'app';
    }
    
    public function setAppPath(string $appPath)
    {
        $this->appPath = path_format($appPath);
    }

    public function getBootPath()
    {
        if (!is_null($this->bootPath)) {
            return $this->bootPath;
        }
        return $this->bootPath = $this->getBasePath() . '/' . 'boot';
    }
    
    public function setBootPath(string $bootPath)
    {
        $this->bootPath = path_format($bootPath);
    }
    
    public function getConfigPath()
    {
        if (!is_null($this->configPath)) {
            return $this->configPath;
        }
        return $this->configPath = $this->getBasePath() . '/' . 'config';
    }
    
    public function setConfigPath(string $configPath)
    {
        $this->configPath = path_format($configPath);
    }
    
    public function getPublicPath()
    {
        if (!is_null($this->publicPath)) {
            return $this->publicPath;
        }
        return $this->publicPath = $this->getBasePath() . '/' . 'public';
    }
    
    public function setRoutePath(string $routePath)
    {
        $this->routePath = path_format($routePath);
    }
    
    public function getRoutePath()
    {
        if (!is_null($this->routePath)) {
            return $this->routePath;
        }
        return $this->routePath = $this->getBasePath() . '/' . 'route';
    }
    
    public function setPublicPath(string $publicPath)
    {
        $this->publicPath = path_format($publicPath);
    }
    
    public function getRuntimePath()
    {
        if (!is_null($this->runtimePath)) {
            return $this->runtimePath;
        }
        return $this->runtimePath = $this->getBasePath() . '/' . 'runtime';
    }
    
    public function setRuntimePath(string $runtimePath)
    {
        $this->runtimePath = path_format($runtimePath);
    }
    
    public function getVendorPath()
    {
        if (!is_null($this->vendorPath)) {
            return $this->vendorPath;
        }
        return $this->vendorPath = $this->getBasePath() . '/' . 'vendor';
    }
    
    public function setVendorPath(string $vendorPath)
    {
        $this->vendorPath = path_format($vendorPath);
    }
    
    public function getEnvPath()
    {
        if (!is_null($this->envPath)) {
            return $this->envPath;
        }
        return $this->envPath = $this->getBasePath();
    }
    
    public function setEnvPath(string $envPath)
    {
        $this->envPath = path_format($envPath);
    }
}