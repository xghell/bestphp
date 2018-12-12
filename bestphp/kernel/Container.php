<?php


namespace Best;

use ArrayAccess;

class Container implements ArrayAccess
{
    /**
     * The instance of self
     *
     * @var
     */
    protected static $instance;

    /**
     * The callable thing that has been bound to the container
     *
     * $closure = [
     *      identifier => closure,
     *      identifier => [class, method],
     *      identifier => function
     * ]
     *
     * @var array
     */
    protected $closures = [];

    /**
     * The class that has been bound to the container
     *
     * $class = [
     *      classname => classname
     * ]
     *
     * @var string[]
     */
    protected $classes = [];

    /**
     * The alias of class that has been bound to the container
     *
     * $aliases = [
     *      alias => classname(that is identifier of class or instance),
     * ]
     *
     * @var string[]
     */
    protected $aliases = [];

    /**
     * The instances that is bound to the container
     *
     * $instances = [
     *      identifier => instance
     * ]
     *
     * @var array
     */
    protected $instances = [];

    /**
     * The concrete EXCLUDE class, closure, instance that has been bound to the container
     *
     * $scalars = [
     *      identifier => concrete
     * ]
     * @var array
     */
    protected $concretes = [];

    private function __construct()
    {
    }

    /**
     * Get thing that has been bound to container. If thing is class, instantiate it and bind to container;
     * If thing is callable, invoke it. If return instance, bind it to container
     *
     * If the class has been bound to container, Support use classname directly as identifier.
     *
     * @param string $identifier
     * @param array $vars
     * @param bool $newInstance
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function get(string $identifier, $vars = [], bool $newInstance = false)
    {
        if (true === $vars) {
            $newInstance = true;
            $vars = [];
        }

        if (isset($this->aliases[$identifier])) {
            $identifier = $this->aliases[$identifier];
        }

        switch ($identifier) {
            case false === $newInstance && (isset($this->instances[$identifier])):
                return $this->instances[$identifier];

            case isset($this->aliases[$identifier]):
                $identifier = $this->aliases[$identifier];
                //no break
            case isset($this->classes[$identifier]):
                return $this->instances[$identifier] = $this->invokeClass($identifier, $vars);

            case isset($this->closures[$identifier]):
                $result = $this->invoke($this->closures[$identifier], $vars);
                if (is_object($result)) {
                    $this->instances[$identifier] = $result;
                }
                return $result;

            case isset($this->concretes[$identifier]):
                return $this->concretes[$identifier];

            default:
                throw new \InvalidArgumentException("The \'{$identifier}\' not bound to container");
        }
    }

    /**
     * Get singleton of the container
     *
     * @return Container
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance ;
    }

    public static function setInstance(Container $instance)
    {
        return static::$instance = $instance;
    }

    /**
     * Bind $concrete to the container with $identifier
     * If bind the same thing to container, the previous one will be covered.
     *
     * @param array|string $identifier
     * @param mixed $concrete
     * @return mixed
     */
    public function bind($identifier, $concrete = null)
    {
        if (is_array($identifier)) {
            foreach ($identifier as $key => $value) {
                $this->bind($key, $value);
            }
            return;
        }

        switch ($concrete) {
            case is_string($concrete) && class_exists($concrete):
                $this->aliases[$identifier] = $concrete;
                $this->classes[$concrete] = $concrete;
                break;
            case is_object($concrete):
                $this->instances[$identifier] = $concrete;
                break;
            case is_callable($concrete):
                $this->closures[$identifier] = $concrete;
                break;
            default:
                $this->concretes[$identifier] = $concrete;
        }
    }

    /**
     * Remove one thing from container
     *
     * @param string $identifier
     */
    public function remove(string $identifier = '')
    {
        if ('' === trim($identifier)) {
            unset($this->aliases);
            unset($this->classes);
            unset($this->instances);
            unset($this->closures);
            unset($this->concretes);
            return;
        }

        switch ($identifier) {
            case isset($this->aliases[$identifier]):
                $identifier = $this->aliases[$identifier];
                //no break
            case isset($this->classes[$identifier]):
                $this->aliases = array_diff($this->aliases, [$identifier]);
                unset($this->classes[$identifier]);
                break;
            case isset($this->instances[$identifier]):
                unset($this->instances[$identifier]);
                break;
            case isset($this->closures[$identifier]):
                unset($this->closures[$identifier]);
                break;
            case isset($this->concretes[$identifier]):
                unset($this->concretes[$identifier]);
        }
    }

    /**
     * Determine whether $identifier has been bound to container
     *
     * @param string $identifier
     * @return bool
     */
    public function has(string $identifier)
    {
        return isset($this->aliases[$identifier])
            || isset($this->classes[$identifier])
            || isset($this->instances[$identifier])
            || isset($this->closures[$identifier])
            || isset($this->concretes[$identifier]);
    }

    /**
     * Instantiate the class
     *
     * @param string $class  The class name
     * @param array $vars   The params needed by construct function when instantiate a class
     * @return object  The instance of class
     * @throws \ReflectionException
     */
    public function invokeClass($class, array $vars = [])
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        //If has no constructor, instantiate class directly;
        if (is_null($constructor)) {
            return $reflection->newInstanceWithoutConstructor();
        }

        $params = $constructor->getParameters();
        $arguments = $this->parseParams($params, $vars, $class);

        return $reflection->newInstanceArgs($arguments);
    }

    public function invoke($callable, array $vars = [])
    {
        if (is_array($callable)) {
            $object = $callable[0];
            $method = $callable[1];

            return $this->invokeMethod($object, $method, $vars);
        }

        return $this->invokeFunction($callable, $vars);
    }

    /**
     * Invoke the specified function
     *
     * @param $function
     * @param array $vars
     * @return mixed
     * @throws \ReflectionException
     */
    protected function invokeFunction($function, array $vars = [])
    {
        $reflection = new \ReflectionFunction($function);
        if (0 === $reflection->getNumberOfParameters()) {
            return $reflection->invoke();
        }

        $params = $reflection->getParameters();
        $arguments = $this->parseParams($params, $vars, $function);

        return $reflection->invokeArgs($arguments);
    }

    /**
     * Invoke the specified method
     *
     * @param $object
     * @param $method
     * @param array $vars
     * @return mixed
     * @throws \ReflectionException
     */
    protected function invokeMethod($object, $method, array $vars = [])
    {
        if (!is_object($object)) {
            $object = $this->invokeClass($object);
        }

        $reflection = new \ReflectionObject($object);

        $method = $reflection->getMethod($method);

        if (0 === $method->getNumberOfParameters()) {
            return $method->invoke($object);
        }

        $params = $method->getParameters();
        $arguments = $this->parseParams($params, $vars, $method);

        return $method->invokeArgs($object, $arguments);
    }

    /**
     * Parse the params
     *
     * @param \ReflectionParameter[] $params
     * @param array $vars
     * @param $exceptionMessage
     * @return array
     * @throws \ReflectionException
     */
    protected function parseParams(array $params, array $vars, $exceptionMessage)
    {
        //Determine if the variables is association array. Yes if $type is 1, or No.
        $type = (0 === key($vars) ? 1 : 0);

        if (1 === $type) {
            return $vars;
        }

        $arguments = [];

        foreach ($params as $param) {
            $name = $param->getName();
            $class = $param->getClass();

            if (isset($vars[$name])) {
                $arguments[] = $vars[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            } elseif ($this->has($name)) {
                $arguments[] = $this->get($name);
            } elseif ($this->has($class->getName())) {
                $arguments[] = $this->get($class->getName());
            } else {
                throw new \InvalidArgumentException("The param({$name}) is required by '$exceptionMessage'");
            }
        }

        return $arguments;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param mixed $offset
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return mixed|void
     */
    public function offsetSet($offset, $value)
    {
        return $this->bind($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}