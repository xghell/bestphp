<?php


namespace Best\Driver\Loader;


use Best\Contract\Loader\LoaderInterface;

class ClassMap implements LoaderInterface
{
    /**
     * Debug mode or not
     *
     * @var bool
     */
    protected $isDebug = false;

    /**
     * The mapping of class name and path
     *
     * @var array
     */
    protected $classMap = [];

    /**
     * ClassMap constructor.
     *
     * @param bool $isDebug
     */
    public function __construct(bool $isDebug = false)
    {
        $this->isDebug = $isDebug;
    }

    /**
     * Register autoload function
     */
    public function register()
    {
        spl_autoload_register([self::class, 'loadClass']);
    }

    /**
     * Add the mapping relationship between classes and paths
     *
     * @param string|array $class
     * @param null|string $path
     * @param bool $prepend
     */
    public function add($class, $path = null, bool $prepend = false)
    {
        $this->checkParam($class, $path);

        if (is_array($class)) {
            if (true === $path) {
                $this->classMap = array_merge($class, $this->classMap);
            } else {
                $this->classMap = array_merge($this->classMap, $class);
            }
            return;
        }

        if (!isset($this->classMap[$class]) || $prepend) {
            $this->classMap[$class] = $path;
        }
    }

    /**
     * Check the parameter of the function 'add'
     *
     * @param string|array $class
     * @param null|string $path
     */
    protected function checkParam($class, $path = null)
    {
        if (!$this->isDebug) {
            return;
        }

        if (is_array($class)) {
            foreach ($class as $key => $value) {
                $this->checkParam($key, $value);
            }
            return;
        }

        if (!is_string($class)) {
            throw new \InvalidArgumentException("The class name($class) must be string");
        }
        if (is_file($path)) {
            throw new \InvalidArgumentException("File($path) not exists");
        }
    }

    /**
     * Autoload class according to class name
     *
     * @param string $classname
     */
    protected function loadClass($classname)
    {
        if (isset($this->classMap[$classname])) {
            include $this->classMap[$classname];
        }
    }
}