<?php


namespace Facade;


use Facade\Contract\Facade;

/**
 * Class Config
 * @package Facade
 *
 * @method static mixed get(string $name = '', $default = null)
 * @method static void  set($name, $value = null)
 * @method static void  remove(string $name = '')
 * @method static bool  has(string $name)
 */
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