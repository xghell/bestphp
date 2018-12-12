<?php


namespace Best\Configure;


class Env implements \ArrayAccess
{
    /**
     * Env variables
     *
     * @var array
     */
    private $items = [];

    /**
     * Env constructor.  Get system env variables.
     */
    public function __construct()
    {
        $items = getenv();
        $this->set($items);
    }
    
    /**
     * Get the item of env variables
     *
     * @param string $name
     * @param null $default
     * @param bool $localOnly
     * @return array|string|null|
     */
    public function get(string $name = '', $default = null, bool $localOnly = false)
    {
        $name = trim($name);
        
        if ('' === $name) {
            return $this->items;
        }

        if ($localOnly) {
            return getenv($name, true) ?? null;
        } else {
            return $this->items[$name] ?? $default;
        }
    }

    /**
     * Set the env variables
     * only support primary configuration, do not support two-level configure
     *
     * @param string|array $name
     * @param string $value
     */
    public function set($name, string $value = '')
    {
        if (is_array($name)) {
            $name = array_change_key_case($name, CASE_UPPER);
            $this->items = array_merge($this->items, $name);
            return;
        }

        $name = trim($name);

        if ('' === $name) {
            throw new \InvalidArgumentException('The name MUST NOT empty.');
        }
        
        $name = strtoupper($name);
        $this->items[$name] = $value;
    }

    /**
     * Remove a env variable
     *
     * @param string $name
     */
    public function remove(string $name = '')
    {
        $name = trim($name);
        
        if ('' === $name) {
            unset($this->items);
            return;
        }
        
        if (isset($this->items[$name])) {
            unset($this->items[$name]);
        }
    }

    /**
     * Determine if the item of env variables exist.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name)
    {
        $name = trim($name);

        if ('' === $name) {
            throw new \InvalidArgumentException('The name MUST NOT empty.');
        }
        
        return isset($this->items[$name]);
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
}