<?php


namespace Best\Kernel;

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
     * The things that has been bound to the container, such as class, closure, resource .etc
     *
     * @var array
     */
    protected $binds = [];

    /**
     * The instances that is bound to the container
     *
     * @var array
     */
    protected $instances = [];

    private function __construct()
    {
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
     * Get thing that has been bound to container
     * if thing is class, instantiate it and bind to container;
     * if thing is closure, invoke it. If return instance, bind it to container
     *
     * @param string $identifier
     * @param array $params
     * @param bool $newInstance
     * @return mixed|object
     * @throws \ReflectionException
     */
    public function get(string $identifier, $params = [], bool $newInstance = false)
    {
        if (true === $params) {
            $newInstance = true;
            $params = [];
        }

        if (isset($this->instances[$identifier]) && false === $newInstance) {
            return $this->instances[$identifier];
        }

        if (isset($this->binds[$identifier])) {
            if ($this->binds[$identifier] instanceof \Closure) {
                $result = $this->invokeFunction($this->binds[$identifier], $params);
                if (is_object($result)) {
                    $this->instances[$identifier] = $result;
                }
                return $result;
            } elseif (class_exists($this->binds[$identifier])) {
                $object = $this->invokeClass($this->binds[$identifier], $params);
                $this->instances[$identifier] = $object;
                return $object;
            } else {
                return $this->binds[$identifier];
            }
        }

        throw new \InvalidArgumentException("The \'{$identifier}\' not bound to container");
    }

    /**
     * Get the class that the identifier correspond to.
     *
     * @param $identifier
     * @return mixed|string
     */
    public function getClass($identifier)
    {
        if (!$this->has($identifier)) {
            throw new \InvalidArgumentException("The \'{$identifier}\' not bound to container");
        }

        if (isset($this->binds[$identifier]) && is_string($this->binds[$identifier])) {
            if (class_exists($this->binds[$identifier])) {
                return $this->binds[$identifier];
            } else {
                throw new \InvalidArgumentException("The class($this->binds[$identifier]) not exists");
            }
        } else {
            return get_class($this->instances[$identifier]);
        }
    }

    /**
     * Get the raw data that bound to container.
     *
     * @param $identifier
     * @return mixed
     */
    public function getRaw($identifier)
    {
        if (isset($this->binds[$identifier])) {
            return $this->binds[$identifier];
        } elseif (isset($this->instances[$identifier])) {
            return $this->instances[$identifier];
        }

        throw new \InvalidArgumentException("The \'{$identifier}\' not bound to container");
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

        if (is_object($concrete)) {
            $this->instances[$identifier] = $concrete;
        } else {
            $this->binds[$identifier] = $concrete;
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
            unset($this->instances);
            unset($this->binds);
            return;
        }

        if (isset($this->instances[$identifier])) {
            unset($this->instances[$identifier]);
        } elseif (isset($this->binds[$identifier])) {
            unset($this->binds[$identifier]);
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
        if (isset($this->instances[$identifier]) || isset($this->binds[$identifier])) {
            return true;
        }
        return false;
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
        $arguments = $this->parseParams($params, $vars);

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * Invoke the specified function
     *
     * @param $function
     * @param array $vars
     * @return mixed
     * @throws \ReflectionException
     */
    public function invokeFunction($function, array $vars = [])
    {
        $reflection = new \ReflectionFunction($function);
        if (0 === $reflection->getNumberOfParameters()) {
            return $reflection->invoke();
        }

        $params = $reflection->getParameters();
        $arguments = $this->parseParams($params, $vars);

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
    public function invokeMethod($object, $method, array $vars = [])
    {
        if (!is_object($object)) {
            $object = $this->invokeClass($object);
        }

        $reflection = new \ReflectionObject($object);

        if (!$reflection->hasMethod($method)) {
            throw new \InvalidArgumentException("The method($method) not exist");
        }

        $method = $reflection->getMethod($method);
        if (0 === $method->getNumberOfParameters()) {
            return $method->invoke($object);
        }

        $params = $method->getParameters();
        $arguments = $this->parseParams($params, $vars);

        return $method->invokeArgs($object, $arguments);
    }

    /**
     * Parse the params
     *
     * @param \ReflectionParameter[] $params
     * @param array $vars
     * @return array
     * @throws \ReflectionException
     */
    protected function parseParams(array $params, array $vars)
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

            if (!is_null($class) && $this->has($class->getName())) {
                $arguments[] = $this->get($class->getName());
            } elseif (isset($vars[$name])) {
                $arguments[] = $vars[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            } else {
                throw new \InvalidArgumentException("The param({$name}) is required");
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