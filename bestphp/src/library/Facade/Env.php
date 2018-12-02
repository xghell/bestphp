<?php


namespace Best\Facade;


use Best\Contract\Facade\Facade;

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