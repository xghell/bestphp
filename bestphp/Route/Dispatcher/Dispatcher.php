<?php


namespace Best\Route\Dispatcher;


use Best\Kernel\Container;

class Dispatcher
{
    /**
     * Dispatch the request and response
     *
     * @param $message
     * @throws \ReflectionException
     */
    public function dispatch($message)
    {
        $responseClass = Container::getInstance()->getClass('response');
        $requestClass = Container::getInstance()->getClass('request');
        if ($message instanceof $responseClass) {
            $message->send();
        } elseif ($message instanceof $requestClass) {
            $result = $this->parseRequest($message);
            Container::getInstance()->get('response', [$result])->send;
//            (new Response($result))->send();
        }
    }

    /**
     * Parse the request
     *
     * @param Request $request
     * @return mixed
     * @throws \ReflectionException
     */
    protected function parseRequest(Request $request)
    {
        $pathInfo = trim($request->getPathInfo(), '/');
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
        
        $controller = 'App\\' . $moduleName . '\\Controller\\' . $controllerName;

        return Container::getInstance()->invokeMethod($controller, $methodName, $arguments);
    }
}