<?php

namespace Best;


class Config
{
    /**
     * The configuration items
     * @var array
     */
    protected $items = [];

    /**
     * Load and parse configure file
     *
     * @param string|array $file  The configure file
     * @param string $type  The type of configure file, such as 'php', 'ini', 'json' .etc
     */
    public function load($file, string $type = '')
    {
        if (is_array($file)) {
            foreach ($file as $value) {
                $this->load($value);
            }
            return;
        }

        if ('' === trim($type)) {
            $type = pathinfo($file, PATHINFO_EXTENSION);
        }

        $name = pathinfo($file, PATHINFO_FILENAME);
        $parser = 'Best\\Driver\\Config\\' . ucfirst($type);

        if (!class_exists($parser)) {
            throw new \InvalidArgumentException("Parser($parser) not found");
        }

        $items = $parser::parse($file);
        $this->set($name, $items);
    }

    /**
     * Get the value of configure item.
     * if $name is empty string , get all configure items.
     * if $name is not empty string and $item is empty string, get the configure item of $name file
     *
     * @param string $name  The name of configure file
     * @param string $item  The configure item
     * @param null $default  If configure not exists, return $default
     * @return array|null
     */
    public function get(string $name = '', string $item = '', $default = null)
    {
        if ('' === trim($name)) {
            return $this->items;
        } elseif ('' === trim($item)) {
            return $this->items[$name] ?? $default;
        }
        return $this->items[$name][$item] ?? $default;
    }

    /**
     * Set the value of configure item
     *
     * @param string|array $name
     * @param string $item
     * @param null $value
     */
    public function set($name, $item = null, $value = null)
    {
        if (is_array($name)) {
            $this->items = array_merge($this->items, $name);
            return;
        }

        if (is_array($item)) {
            $this->items[$name] = !isset($this->items[$name]) ? $item : array_merge($this->items[$name], $item);
            return;
        }

        if (is_array($value)) {
            $this->items[$name][$item] = !isset($this->items[$name][$item]) ? $value : array_merge($this->items[$name][$item], $value);
            return;
        }

        $this->items[$name][$item] = $value;
    }

    /**
     * remove a configure item
     *
     * @param string $name
     * @param string $item
     */
    public function remove(string $name = '', string $item = '')
    {
        if ('' === $name) {
            unset($this->items);
            return;
        }
        
        if ('' === $item) {
            unset($this->items[$name]);
            return;
        } 
        
        if (isset($this->items[$name][$item])) {
            unset($this->items[$name][$item]);
        }
    }

    /**
     * Determine whether the configure item exists or not
     *
     * @param string $name
     * @param string $item
     * @return bool
     */
    public function has(string $name, string $item = '')
    {
        if ('' !== $item) {
            return isset($this->items[$name][$item]);
        }
        
        return isset($this->items[$name]);
    }
}