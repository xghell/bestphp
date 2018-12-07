<?php


namespace Best\Facade;


use Best\Facade\Contract\Facade;

class Env extends Facade
{
    /**
     * @return mixed
     */
    protected static function getFacadeClass()
    {
        return 'env';
    }
}