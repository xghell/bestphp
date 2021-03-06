<?php


namespace Best\Driver\Loader;


use Best\Contract\Loader\LoaderInterface;

class Psr0 implements LoaderInterface
{
    /**
     * Debug mode or not
     *
     * @var bool
     */
    protected $isDebug = false;

    /**
     * the namespace prefixes correspond to directories
     *
     * @var array
     */
    protected $prefixDir = [];

    /**
     * the directory that do not correspond to namespace prefix
     *
     * @var array
     */
    protected $fallbackDir = [];

    /**
     * Psr0 constructor.
     *
     * @param bool $isDebug
     */
    public function __construct(bool $isDebug = false)
    {
        $this->isDebug = $isDebug;
    }

    /**
     * register autoload function
     */
    public function register()
    {
        spl_autoload_register([self::class, 'loadClass']);
    }

    /**
     * unregister autoload function
     */
    public function unregister()
    {
        spl_autoload_unregister([self::class, 'loadClass']);
    }

    /**
     * add the namespace prefix
     *
     * @param string|array $prefix
     * @param string|array|null $rootDir
     * @param bool $prepend
     */
    public function add($prefix, $rootDir = null, bool $prepend = false)
    {
        $rootDir = (array) $rootDir;

        $this->checkParam($prefix, $rootDir);

        if (is_array($prefix)) {
            if (true === $rootDir) {
                $this->prefixDir = array_merge($prefix, $this->prefixDir);
            } else {
                $this->prefixDir = array_merge($this->prefixDir, $prefix);
            }
            return;
        }

        if ('' === trim($prefix)) {
            if ($prepend) {
                $this->fallbackDir = array_merge($rootDir, $this->fallbackDir);
            } else {
                $this->fallbackDir = array_merge($this->fallbackDir, $rootDir);
            }
        } else {
            if (isset($this->prefixDir[$prefix])) {
                if ($prepend) {
                    $this->prefixDir[$prefix] = array_merge($rootDir, $this->prefixDir[$prefix]);
                } else {
                    $this->prefixDir[$prefix] = array_merge($this->prefixDir[$prefix], $rootDir);
                }
            } else {
                $this->prefixDir[$prefix] = $rootDir;
            }
        }
    }
    
    /**
     * check param format
     *
     * @param string|array $prefix
     * @param null|array $rootDir
     */
    protected function checkParam($prefix, $rootDir = null)
    {
        if (!$this->isDebug) {
            return;
        }

        if (is_array($prefix)) {
            foreach ($prefix as $key => $value) {
                if ('' === trim($key)) {
                    throw new \InvalidArgumentException("Prefix must not be empty string");
                }
                $this->checkParam($key, $value);
            }
            return;
        }

        if (!is_array($rootDir)) {
            throw new \InvalidArgumentException("A array was required instead of $rootDir");
        }
        foreach ($rootDir as $dir) {
            if (!is_dir($dir)) {
                throw new \InvalidArgumentException("Directory($dir) not exists.");
            }
        }
    }

    /**
     * Autoload function
     *
     * @param $classname
     */
    protected function loadClass($classname)
    {
        $file = $this->findFile($classname);
        if (false !== $file) {
            include $file;
        }
    }

    /**
     * Find file by class name
     *
     * @param $classname
     * @return bool|string
     */
    protected function findFile($classname)
    {
        $logicPath = strtr($classname, '\\', DIRECTORY_SEPARATOR) . '.php';
        $pos = strrpos($logicPath, '/');
        $logicPath = substr($logicPath, 0, $pos) . strtr(substr($logicPath, $pos), '_', DIRECTORY_SEPARATOR);
        $subPath = $logicPath;
        while (false !== ($pos = strrpos($subPath, '/'))) {
            $subPath = substr($subPath, 0, $pos);
            $prefix = substr($classname, 0, $pos + 1);
            
            $prefix = isset($this->prefixDir[rtrim($prefix, '\\')]) ? rtrim($prefix) : $prefix;

            if (isset($this->prefixDir[$prefix])) {
                foreach ($this->prefixDir[$prefix] as $rootDir) {
                    $file = $rootDir . '/' . $logicPath;
                    if (is_file($file)) {
                        return $file;
                    }
                }
            }
        }

        foreach ($this->fallbackDir as $rootDir) {
            $file = $rootDir . '/' . $logicPath;
            if (is_file($file)) {
                return $file;
            }
        }
        return false;
    }
}
