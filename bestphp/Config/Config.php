<?php

namespace Best\Config;


use Best\File\Filesystem;
use Best\Kernel\Container;

class Config implements \ArrayAccess
{
    /**
     * The configuration items
     *
     * @var array
     */
    private $items;

    /**
     * Load the configure file in the specified directory
     *
     * @param string $path
     */
    public function loadDir(string $path)
    {
        $path = path_format($path);
        if (!is_dir($path)) {
            throw new \InvalidArgumentException("The path($path) is not a directory");
        }

        $files = array_slice(scandir($path), 2);
        foreach ($files as $file) {
            $this->loadFile($path . '/' . $file);
        }
    }

    /**
     * Load and parse configure file
     *
     * @param string|array $file  The configure file
     * @param string $type  The type of configure file, such as 'php', 'ini', 'json' .etc
     * @throws \ReflectionException
     */
    public function loadFile($file, string $type = '')
    {
        if (is_array($file)) {
            foreach ($file as $value) {
                $this->loadFile($value);
            }
            return;
        }

        $filesystem = Container::getInstance()->get('filesystem');

        $name = pathinfo($file, PATHINFO_FILENAME);
        
        $items = $filesystem->parse($type);
        $this->set($name, $items);
    }

    /**
     * Get the value of configure item.
     * if $name is empty string , get all configure items.
     *
     * @param string $name  The name of configure file
     * @param null|mixed $default  If configure not exists, return $default
     * @return array|null|mixed
     */
    public function get(string $name = '', $default = null)
    {
        $name = rtrim($name, '. ');

        if ('' === $name) {
            return $this->items;
        }

        if (0 === strpos($name, '.')) {
            throw new \InvalidArgumentException('The first char must not be a dot.');
        }

        $info = explode('.', $name);
        switch (count($info)) {
            case 1:
                return $this->items[$name] ?? $default;
            case 2:
                list($name, $item) = $info;
                return $this->items[$name][$item] ?? $default;
            case 3:
                list($name, $item, $key) = $info;
                return $this->items[$name][$item][$key] ?? $default;
            default:
                throw new \InvalidArgumentException('Only support THREE-LEVEL configure');
        }
    }

    /**
     * Set the value of configure item
     *
     * @param string $name
     * @param null|mixed|array $value
     */
    public function set(string $name, $value = null)
    {
        $name = rtrim($name, '. ');

        if ('' === $name) {
            throw new \InvalidArgumentException('The name MUST NOT empty.');
        }

        if (0 === strpos($name, '.')) {
            throw new \InvalidArgumentException('The first char of name must not be a dot.');
        }

        $info = explode('.', $name);
        switch (count($info)) {
            case 1:
                if (is_array($value)) {
                    if (isset($this->items[$name])) {
                        $this->items[$name] = $value + $this->items[$name];
                    } else {
                        $this->items[$name] = $value;
                    }
                } else {
                    throw new \InvalidArgumentException('The value MUST be a array.');
                }
                return;
            case 2:
                list($name, $item) = $info;
                if (is_array($value) && isset($this->items[$name][$item])) {
                    $this->items[$name][$item] = $value + $this->items[$name][$item];
                } else {
                    $this->items[$name][$item] = $value;
                }
                return;
            case 3:
                list($name, $item, $key) = $info;
                if (is_array($value) && isset($this->items[$name][$item][$key])) {
                    $this->items[$name][$item][$key] = $value + $this->items[$name][$item][$key];
                } else {
                    $this->items[$name][$item][$key] = $value;
                }
                return;
            default:
                throw new \InvalidArgumentException('Only support THREE-LEVEL configure');
        }
    }

    /**
     * Remove a configure item
     *
     * @param string $name
     */
    public function remove(string $name = '')
    {
        $name = rtrim($name, '. ');

        if ('' === $name) {
            unset($this->items);
            return;
        }

        if (0 === strpos($name, '.')) {
            throw new \InvalidArgumentException('The first char of name MUST NOT be a dot.');
        }

        $info = explode('.', $name);
        switch (count($info)) {
            case 1:
                unset($this->items[$name]);
                return;
            case 2:
                list($name, $item) = $info;
                unset($this->items[$name][$item]);
                return;
            case 3:
                list($name, $item, $key) = $info;
                unset($this->items[$name][$item][$key]);
                return;
            default:
                throw new \InvalidArgumentException('Only support THREE-LEVEL configure');
        }
    }

    /**
     * Determine whether the configure item exists or not
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name)
    {
        $name = rtrim($name, '. ');

        if ('' === $name) {
            throw new \InvalidArgumentException('The name MUST NOT be empty');
        }

        if (0 === strpos($name, '.')) {
            throw new \InvalidArgumentException('The first char must not be a dot.');
        }

        $info = explode('.', $name);
        switch (count($info)) {
            case 1:
                return isset($this->items[$name]);
            case 2:
                list($name, $item) = $info;
                return isset($this->items[$name][$item]);
            case 3:
                list($name, $item, $key) = $info;
                return isset($this->items[$name][$item][$key]);
            default:
                throw new \InvalidArgumentException('Only support THREE-LEVEL configure');
        }
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
        return $this->remove($offset);
    }
}