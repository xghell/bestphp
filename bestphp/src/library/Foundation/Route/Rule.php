<?php


namespace Best\Foundation\Route;


class Rule
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $wheres;

    /**
     * @var array
     */
    private $options;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function name(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getWhere()
    {
        return $this->wheres;
    }

    /**
     * @param string|array $name
     * @param string|null $pattern
     * @return $this
     */
    public function where($name, $pattern = null)
    {
        if (is_array($name)) {
            $this->wheres = $name + ($this->wheres ?? []);
        } else {
            $this->wheres[$name] = $pattern;
        }

        return $this;
    }

    /**
     * @param string $name
     * @return array|string|null
     */
    public function getOption(string $name = '')
    {
        if ('' === trim($name)) {
            return $this->options;
        }

        return $this->options[$name] ?? null;
    }

    /**
     * @param string|array $name
     * @param $value
     * @return $this
     */
    public function option($name, $value = null)
    {
        if (is_array($name)) {
            $this->options = $name + ($this->options ?? []);
        } else {
            $this->options[$name] = $value;
        }

        return $this;
    }
}