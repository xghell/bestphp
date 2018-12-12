<?php


namespace Best\Service;


use Best\Configure\Config;
use Best\Configure\Env;
use Best\Filesystem\Filesystem;
use Best\Http\Request;
use Best\Http\Response;
use Best\Service\Contract\Service;
use Best\Pipeline\Pipeline;
use Best\Validate\Validator;
use Best\View\View;

class FoundationService extends Service
{
    /**
     * Run the service
     *
     * @return mixed
     */
    public function register()
    {
        $this->app->bind([
            'pipeline'      => Pipeline::class,
            'validator'     => Validator::class,
            'config'        => Config::class,
            'env'           => Env::class,
            'filesystem'    => Filesystem::class,
            'request'       => Request::class,
            'response'      => Response::class,
            'view'          => View::class
        ]);
    }

    /**
     * Bootstrap the service
     *
     * @return mixed
     */
    public function boot()
    {
        //load the global configuration files
        $this->app['config']->set($this->app['filesystem']->parseDir($this->app->configPath()));
        //load the environment variables and '.env' file;
        $this->app['env']->set($this->app['filesystem']->parseFile($this->app->envPath() . '/' . '.env', 'ini'));
    }
}