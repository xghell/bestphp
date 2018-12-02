<?php

namespace Best\Contract\Container;

interface ContainerInterface
{
    /**
     * Get an instance of class or closure, or get a instance that has been bound to container directly.
     *
     * @param $identifier
     * @param array $param  The parameters required when instantiate $identifier
     * @param bool $new  Return a new instance or not
     * @return mixed
     */
    public function get(string $identifier, $param, bool $new = false);

    /**
     * Bind a class, closure, instance, resource .etc to container
     * 
     * @param string|array $identifier   The identifier of $concrete
     * @param mixed $concrete   A class, closure or instance
     * @return mixed
     */
    public function bind($identifier, $concrete);
    
    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $identifier Identifier of the entry to look for.
     * @return bool
     */
    public function has(string $identifier);
}
