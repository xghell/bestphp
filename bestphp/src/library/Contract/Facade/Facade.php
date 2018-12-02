<?php


namespace Best\Contract\Facade;

/**
 * If you always want to gain a new instance, do not use facade.
 *
 * Class Facade
 * @package Best
 */
abstract class Facade
{
    /**
     * Get the the class name or class identifier in container
     *
     * @example
     * public function getFacadeClass()
     * {
     *      return 'app'
     * }
     * public function getFacadeClass()
     * {
     *      return '\Best\Config'
     * }
     *
     * @return string   Return the class name or class identifier in container.
     */
    abstract protected static function getFacadeClass();

    /**
     * Create a facade class instance.
     *
     * @return mixed|object
     * @throws \ReflectionException
     */
    protected static function createFacadeInstance()
    {
        $class = static::getFacadeClass();

        if (Container::getInstance()->has($class)) {
            return Container::getInstance()->get($class);
        } else {
            $instance = new $class();
            Container::getInstance()->bind($class, $instance);
            return $instance;
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    public static function __callStatic($name, $arguments)
    {
        $instance = static::createFacadeInstance();
        return call_user_func_array([$instance, $name], $arguments);
    }
}