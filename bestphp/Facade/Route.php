<?php


namespace Best\Facade;


use Best\Facade\Contract\Facade;

/**
 * Class Route
 * @package Best\Facade
 * @see \Best\Router
 * @method \Best\Route\Rule\Route get(string $path, $action)
 */
class Route extends Facade
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
    protected static function getFacadeClass()
    {
        return 'router';
    }
}