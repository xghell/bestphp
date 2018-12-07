<?php


namespace Best\Kernel\Service;


use Best\Http\Request;
use Best\Http\Response;
use Best\Kernel\Service\Contract\Service;

class HttpService extends Service
{
    /**
     * Run the service
     *
     * @return mixed
     */
    public function run()
    {
        $this->app->bind([
            'request'   => Request::class,
            'response'  => Response::class
        ]);
    }
}