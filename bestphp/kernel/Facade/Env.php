<?php


namespace Best\Facade;


use Best\Facade\Contract\Facade;

/**
 * Class Env
 * @package Best\Facade
 *
 * @method static mixed get(string $name = '', $default = null, bool $localOnly = false)
 * @method static void  set($name, string $value = '')
 * @method static void  remove(string $name = '')
 * @method static bool  has(string $name)
 */
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