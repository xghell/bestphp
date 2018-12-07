<?php


namespace Best\Route;

use Best\Http\Request;
use Best\Http\Response;
use Best\Kernel\Container;
use Best\Route\Rule\Route;
use Best\Route\Rule\RouteGroup;

class Router
{
    private $config = [
        //The default variables pattern
        'default_pattern' => '([\w-]+)',
        //determine if must completely match
        'strict'          => false
    ];

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
    
    public function load(string $path)
    {
        $path = path_format($path);
        if (is_dir($path)) {
            $files = array_slice(scandir($path), 2);
            foreach ($files as $file) {
                require $path . '/' . $file;
            }
        } elseif (is_file($path)) {
            require $path;
        }

    }

    /**
     * Check whether the request conforms to routing rules
     *
     * @param Request $request
     * @return mixed   Return False or [$route, $vars]
     */
    public function check(Request $request)
    {
        $requestPath = $request->getPathInfo();
        $requestMethod = $request->getMethod();

        foreach (($this->routes ?? []) as $route) {
            $route = $this->parseGroup($route);
            $strict = $route->getOption('strict') ?? $this->config['strict'];
            
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
     * @param $requestPath
     * @param Route $route
     * @param bool $strict
     * @param array $vars
     * @param array $replaceParis
     * @return bool
     */
    protected function checkPath($requestPath, Route $route, bool $strict = false, &$vars = [], &$replaceParis = [])
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
        array_walk($allowedMethod, function (&$value) {
            $value = strtoupper($value);
        });
        if (in_array($requestMethod, $allowedMethod)) {
            return true;
        }

        return false;
    }

    /**
     * Merge the RouteGroup with Route.
     *
     * @param Route $route
     * @return Route
     */
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
     * Parse the route action.
     *
     * @param $action
     * @param $vars
     * @param $replaceParis
     * @param Request $request
     * @return Request|Response
     */
    protected function parseAction($action, $vars, $replaceParis, Request $request)
    {
        if ($action instanceof \Closure) {
            $result = call_user_func_array($action, $vars);
            return new Response($result);
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
     * @param array $varNames
     * @param array $replaceNames
     * @return string
     */
    protected function parsePath(Route $route, bool $strict, &$varNames = [], &$replaceNames = [])
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
        $defaultWhere = array_pad([], $varCount, $this->config['default_pattern']);
        $replaceParis = $where + array_combine($replaceNames, $defaultWhere);

        return '#^' . strtr($path, $replaceParis) . ($strict ? '$' : '') . '#';
    }

    /**
     * Add the route that match multiple request method
     *
     * @param array $methods
     * @param string $path
     * @param $action
     * @return Route
     * @throws \ReflectionException
     */
    public function match(array $methods, string $path, $action)
    {
        return $this->addRoute($path, $action, $methods);
    }

    /**
     * Add the route that match of request method.
     *
     * @param string $path
     * @param $action
     * @return Route
     * @throws \ReflectionException
     */
    public function any(string $path, $action)
    {
        return $this->addRoute($path, $action, '*');
    }

    /**
     * Add the route that match GET method
     *
     * @param string $path
     * @param $action
     * @return Route
     * @throws \ReflectionException
     */
    public function get(string $path, $action)
    {
        return $this->addRoute($path, $action, 'get');
    }

    /**
     * Add the route that match the POST method
     *
     * @param string $path
     * @param $action
     * @return Route
     * @throws \ReflectionException
     */
    public function post(string $path, $action)
    {
        return $this->addRoute($path, $action, 'post');
    }

    /**
     * Add the route that matches PUT method
     *
     * @param string $path
     * @param $action
     * @return Route
     * @throws \ReflectionException
     */
    public function put(string $path, $action)
    {
        return $this->addRoute($path, $action);
    }

    /**
     * Add the route that matches DELETE method
     *
     * @param string $path
     * @param $action
     * @return Route
     * @throws \ReflectionException
     */
    public function delete(string $path, $action)
    {
        return $this->addRoute($path, $action, 'delete');
    }

    /**
     * Add the route group
     *
     * @param string $path
     * @param \Closure $action
     * @return RouteGroup
     */
    public function group(string $path, \Closure $action)
    {
        Container::getInstance()->bind('route_group1234567890', $path);
        call_user_func($action);
        return $this->routeGroups[$path] = (new RouteGroup($path));
    }

    /**
     * Add the route.
     *
     * @param string $path
     * @param $action
     * @param $methods
     * @return Route
     * @throws \ReflectionException
     */
    protected function addRoute(string $path, $action, $methods)
    {
        if (Container::getInstance()->has('route_group1234567890')) {
            return $this->routes[] = (new Route($path, $action))->group(Container::getInstance()->get('route_group1234567890'))
                ->option('method', $methods);
        } else {
            return $this->routes[] = (new Route($path, $action))->option('method', $methods);
        }
    }
}