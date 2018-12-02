<?php


namespace Best;

/**
 * Class Env  only support primary configuration, do not support two-level configure
 *
 * @package Best
 */
class Env
{
    /**
     * Env variables
     *
     * @var array
     */
    protected $items = [];

    /**
     * Env constructor.  Get system env variables.
     */
    public function __construct()
    {
        $items = getenv();
        $this->set($items);
    }

    /**
     * Load .env file
     *
     * @param $file
     */
    public function load($file)
    {
        $items = parse_ini_file($file);
        $this->set($items);
    }

    /**
     * @param string $name
     * @param null $default
     * @param bool $local_only
     * @return array|false|mixed|null|string
     */
    public function get(string $name = '', $default = null, bool $local_only = false)
    {
        if ('' === $name) {
            return $this->items;
        }

        if ($local_only) {
            return getenv($name, true);
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
        if ('' === $name) {
            unset($this->items);
            return;
        }
        
        if (isset($this->items[$name])) {
            unset($this->items[$name]);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name)
    {
        return isset($this->items[$name]);
    }
}