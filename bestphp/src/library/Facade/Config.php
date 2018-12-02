<?php


namespace Best\Facade;


use Best\Contract\Facade\Facade;

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