<?php


namespace Best\Facade;


use Best\Facade\Contract\Facade;

/**
 * Class Route
 * @package Best\Facade
 *
 * @method mixed load(string $path) Load the route configure file
 * @method Best\Route\Dispatch\Contract\Dispatch dispatch($request)
 * @method \Best\Route\Rule\Route match(array $methods, string $path, $action)
 * @method \Best\Route\Rule\Route any(string $path, $action)
 * @method \Best\Route\Rule\Route get(string $path, $action)
 * @method \Best\Route\Rule\Route post(string $path, $action)
 * @method \Best\Route\Rule\Route put(string $path, $action)
 * @method \Best\Route\Rule\Route delete(string $path, $action)
 * @method \Best\Route\Rule\Route group(string $path, \Closure $action)
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
     *      return 'Best\Configure\Config'
     * }
     *
     * @return string   Return the class name or class identifier in container.
     */
    protected static function getFacadeClass()
    {
        return 'router';
    }
}