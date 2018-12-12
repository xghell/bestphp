<?php


namespace Best\Route\Dispatch;


use Best\Container;
use Best\Route\Dispatch\Contract\Dispatch;

class Controller implements Dispatch
{
    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $arguments;

    /**
     * Controller constructor.
     *
     * @param string $action
     * @param array $vars
     */
    public function __construct(string $action, array $vars)
    {
        $this->parseAction($action, $vars);
    }

    /**
     * Execute the dispatch action, return the http response message
     *
     * @return mixed
     */
    public function execute()
    {
        return Container::getInstance()->invoke([$this->controller, $this->method], $this->arguments);
    }

    /**
     * Parse the action with variables to module, controller, method, arguments
     *
     * @param string $action
     * @param array $vars
     */
    protected function parseAction(string $action, array $vars)
    {
        $pathInfo = trim($action, '/');
        $dispatchInfo = explode('/', $pathInfo);
        $moduleName = ucfirst(array_shift($dispatchInfo));
        $controllerName = ucfirst(array_shift($dispatchInfo));
        $methodName = array_shift($dispatchInfo);

        $argumentKeys = [];
        $argumentValues = [];

        array_walk($dispatchInfo, function ($value, $key) use (&$argumentKeys, &$argumentValues) {
            if (!($key & 1)) {
                $argumentKeys[] = $value;
            } else {
                $argumentValues[] = $value;
            }
        });

        $arguments = array_combine($argumentKeys, $argumentValues);

        $this->module = $moduleName;
        $this->controller = 'App\\' . $moduleName . '\\Controller\\' . $controllerName;
        $this->method = $methodName;
        $this->arguments = $vars + $arguments;
    }
}