<?php


namespace Best;

use Best\Foundation\Route\Route;
use Best\Foundation\Route\RouteGroup;

class Router
{
    /**
     * The default variables pattern
     *
     * @var string
     */
    protected $defaultPattern = '([\w-]+)';

    /**
     * The array of the instance of Route
     *
     * @var Route[]
     */
    protected $routes;

    /**
     * @var Route[]
     */
    protected $routeGroups;

    /**
     * Check whether the request conforms to routing rules
     *
     * @param Request $request
     * @param bool $strict
     * @return mixed   Return False or [$route, $vars]
     */
    public function check(Request $request, bool $strict = false)
    {
        $requestPath = $request->getPathinfo();
        $requestMethod = $request->getMethod();

        foreach (($this->routes ?? []) as $route) {
            $route = $this->parseGroup($route);
            if ($this->checkMethod($requestMethod, $route) &&
                $this->checkPath($requestPath, $route, $strict, $vars, $replacePairs)
            ) {
                $action = $route->getAction();

                return $this->parseAction($action, $vars, $replacePairs, $request);
            }
        }
        
        return false;
    }

    /**
     * Check if the request path conform the route path
     *
     * @param string $requestPath
     * @param Route $route
     * @param bool $strict
     * @param $matches
     * @return bool
     */
    protected function checkPath($requestPath, Route $route, bool $strict = false, &$vars, &$replaceParis)
    {
        $routePath = $this->parsePath($route, $strict, $varNames, $replaceNames);
        if (preg_match($routePath, $requestPath, $matches)) {
            array_shift($matches);
            $vars = array_combine($varNames, $matches);
            $replaceParis = array_combine($replaceNames, $matches);
            return true;
        }

        return false;
    }

    /**
     * Check if the request path conform the route allowed methods.
     *
     * @param string $requestMethod
     * @param Route $route
     * @return bool
     */
    protected function checkMethod($requestMethod, Route $route)
    {
        $allowedMethod = $route->getOption('method');
        if ('*' === $allowedMethod) {
            return true;
        }

        $requestMethod = strtoupper($requestMethod);
        $allowedMethod = (array) $allowedMethod;
        array_walk($allowedMethod, function (&$value, $key) {
            $value = strtoupper($value);
        });
        if (in_array($requestMethod, $allowedMethod)) {
            return true;
        }

        return false;
    }
    
    protected function parseGroup(Route $route)
    {
        $groupName = $route->getGroup();
        
        if (is_null($groupName)) {
            return $route;
        }
        
        $group = $this->routeGroups[$groupName] ?? null;

        if (!is_null($group)) {
            $path = $group->getName() . $route->getPath();
            $wheres = ($route->getWhere() ?? []) + ($group->getWhere() ?? []);
            $options = ($route->getOption() ?? []) + ($group->getOption() ?? []);

            return $route->withPath($path)
                ->where($wheres)
                ->option($options);
        }

        return $route;
    }

    /**
     * @param $action
     * @param $vars
     * @param Request $request
     * @return Request|mixed
     */
    protected function parseAction($action, $vars, $replaceParis, Request $request)
    {
        if ($action instanceof \Closure) {
            return call_user_func_array($action, $vars);
        } elseif ($action instanceof Response) {
            return $action;
        } elseif (is_string($action)) {
            $action = strtr($action, $replaceParis);
            return $request->withUri($action)
                ->withGet($vars);
        }
    }

    /**
     * Parse the route path rule.
     *
     * $defaultPattern = '([\w-]+)'
     *
     * $path = '/user/{name}-{nickname}/age-{age}/{gender}';
     *
     * $where = [
     *      '{name}' => '(\w+)',
     *      '{age}'  => '(\d{1,2})'
     * ];
     *
     * $resolvedPath = '#/user/(\w+)-([\w-]+)/age-(\d{1,2})/([\w-]+)#';
     *
     * @param Route $route
     * @param bool $strict
     * @return string
     */
    protected function parsePath(Route $route, bool $strict, &$varNames = [], &$replaceNames)
    {
        $path = $route->getPath();
        $where = [];

        foreach (($route->getWhere() ?? []) as $key => $value) {
            $key = '{' . $key . '}';
            $where[$key] = '(' . $value . ')';
        }

        preg_match_all('/\{(\w+?)\}/', $path, $matches);
        $varNames = $matches[1];
        $replaceNames = $matches[0];

        $varCount = count($replaceNames);
        $defaultWhere = array_pad([], $varCount, $this->defaultPattern);
        $replaceParis = $where + array_combine($replaceNames, $defaultWhere);

        return '#^' . strtr($path, $replaceParis) . ($strict ? '$' : '') . '#';
    }

    /**
     * @param array $methods
     * @param string $path
     * @param $action
     * @return mixed
     */
    public function match(array $methods, string $path, $action)
    {
        return $this->addRoute($path, $action, $methods);
    }

    /**
     * @param string $path
     * @param $action
     * @return mixed
     */
    public function any(string $path, $action)
    {
        return $this->addRoute($path, $action, '*');
    }

    /**
     * @param string $path
     * @param $action
     * @return mixed
     */
    public function get(string $path, $action)
    {
        return $this->addRoute($path, $action, 'get');
    }

    public function post(string $path, $action)
    {
        return $this->addRoute($path, $action, 'post');
    }

    public function put(string $path, $action)
    {
        return $this->addRoute($path, $action);
    }

    public function delete(string $path, $action)
    {
        return $this->addRoute($path, $action, 'delete');
    }

    public function group(string $path, \Closure $action)
    {
        $GLOBALS['route_group1234567890'] = '/' . trim($path, '/');
        call_user_func($action);
        return $this->routeGroups[$path] = (new RouteGroup($path));
    }

    protected function addRoute(string $path, $action, $methods)
    {
        return $this->routes[] = (new Route($path, $action))->group($GLOBALS['route_group1234567890'] ?? null)
            ->option('method', $methods);
    }
}