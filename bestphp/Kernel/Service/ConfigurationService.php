<?php


namespace Best\Kernel\Service;


use Best\Env\Env;
use Best\Config\Config;
use Best\Kernel\Service\Contract\Service;

class ConfigurationService extends Service
{
    /**
     * Run the service
     *
     * @return mixed
     */
    public function run()
    {
        $this->app->bind([
            'env'     => Env::class,
            'config'  => Config::class,
        ]);
    }
}