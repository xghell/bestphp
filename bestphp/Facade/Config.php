<?php


namespace Best\Facade;


use Best\Facade\Contract\Facade;

class Config extends Facade
{
    /**
     * @return mixed
     */
    protected static function getFacadeClass()
    {
        return 'config';
    }
}