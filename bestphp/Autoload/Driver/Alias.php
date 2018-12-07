<?php


namespace Best\Autoload\Driver;


use Best\Autoload\Contract\Loader;

class Alias extends Loader
{
    /**
     * The class alias
     *
     * @var string[]
     */
    private $alias;

    /**
     * Register autoload
     *
     * @return mixed|void
     */
    public function register()
    {
        foreach (($this->alias ?? []) as $name => $class) {
            class_alias($class, $name);
        }
    }

    /**
     * Add the alias of the class.
     *
     * @param $alias
     * @param null $class
     * @param bool $prepend
     * @return mixed|void
     */
    public function add($alias, $class = null, bool $prepend = false)
    {
        if ($prepend) {
            if (is_array($alias)) {
                $this->alias = $alias + ($this->alias ?? []);
            } else {
                $this->alias[$alias] = $class;
            }
        } else {
            if (is_array($alias)) {
                $this->alias = ($this->alias ?? []) + $alias;
            } elseif (!isset($this->alias[$alias])) {
                $this->alias[$alias] = $class;
            }
        }
    }

    /**
     * If debug model, Check the params of the function 'add'.
     *
     * @param string|array $alias
     * @param string $class
     * @return mixed
     */
    protected function checkParam($alias, $class)
    {
        if (!$this->isDebug()) {
            return;
        }

        if (is_array($alias)) {
            foreach ($alias as $key => $value) {
                $this->checkParam($key, $value);
            }
            return;
        }

        if ('' === $alias) {
            throw new \InvalidArgumentException('The alias MUST NOT be empty.');
        }

        if (class_exists($class)) {
            throw new \InvalidArgumentException("The class($class) not exists");
        }
    }
}