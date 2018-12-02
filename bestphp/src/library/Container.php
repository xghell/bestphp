<?php


namespace Best;

use ArrayAccess;
use Best\Contract\Container\ContainerInterface;

class Container implements ContainerInterface, ArrayAccess
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

    /**
     * Privatize constructor
     *
     * Container constructor.
     */
    private function __construct()
    {
    }

    /**
     * Private clone function
     */
    private function __clone()
    {
    }

    /**
     * Get singleton of the container
     *
     * @return Container
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance ;
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
                $result = call_user_func_array($this->binds[$identifier], $params);
                if (is_object($result)) {
                    $this->instances[$identifier] = $result;
                }
                return $result;
            } elseif (class_exists($this->binds[$identifier])) {
                $object = $this->instantiateClass($this->binds[$identifier], $params);
                $this->instances[$identifier] = $object;
                return $object;
            } else {
                return $this->binds[$identifier];
            }
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
    public function bind($identifier, $concrete)
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
        if (isset($this->classes[$identifier]) || isset($this->instances[$identifier]) || isset($this->closures[$identifier])) {
            return true;
        }
        return false;
    }

    /**
     * @param string $class  The class name
     * @param array $params   The params needed by construct function when instantiate a class
     * @return object  The instance of class
     * @throws \ReflectionException
     */
    protected function instantiateClass($class, $params = [])
    {
        $params = (array) $params;
        return (new \ReflectionClass($class))->newInstanceArgs($params);
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