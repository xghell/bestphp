<?php


namespace Best\Kernel;


use Best\Validate\Validator;
use Best\Kernel\Service\Contract\Service;

class ValidateService extends Service
{
    /**
     * Run the service
     *
     * @return mixed
     */
    public function run()
    {
        $this->app->bind('validator', Validator::class);
    }
}