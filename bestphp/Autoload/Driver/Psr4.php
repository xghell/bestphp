<?php


namespace Best\Autoload\Driver;


use Best\Autoload\Contract\Loader;

class Psr4 extends Loader
{
    /**
     * The namespace prefixes correspond to directories
     *
     * @var array
     */
    protected $prefixDir = [];

    /**
     * The directory that do not correspond to namespace prefix
     *
     * @var array
     */
    protected $fallbackDir = [];

    /**
     * Register autoload function
     */
    public function register()
    {
        spl_autoload_register([self::class, 'loadClass']);
    }

    /**
     * Add the namespace prefix
     *
     * @param string|array $prefix
     * @param string|array|null $baseDir
     * @param bool $prepend
     */
    public function add($prefix, $baseDir = null, bool $prepend = false)
    {
        $baseDir = (array) $baseDir;

        $this->checkParam($prefix, $baseDir);

        if (is_array($prefix)) {
            if (true === $baseDir) {
                $this->prefixDir = array_merge($prefix, $this->prefixDir);
            } else {
                $this->prefixDir = array_merge($this->prefixDir, $prefix);
            }
            return;
        }

        if ('' === trim($prefix)) {
            if ($prepend) {
                $this->fallbackDir = array_merge($baseDir, $this->fallbackDir);
            } else {
                $this->fallbackDir = array_merge($this->fallbackDir, $baseDir);
            }
        } else {
            if (isset($this->prefixDir[$prefix])) {
                if ($prepend) {
                    $this->prefixDir[$prefix] = array_merge($baseDir, $this->prefixDir[$prefix]);
                } else {
                    $this->prefixDir[$prefix] = array_merge($this->prefixDir[$prefix], $baseDir);
                }
            } else {
                $this->prefixDir[$prefix] = $baseDir;
            }
        }
    }

    /**
     * Check the parameter of function 'add'
     *
     * @param string|array $prefix
     * @param null|array $baseDir
     */
    protected function checkParam($prefix, $baseDir = null)
    {
        if (!$this->appDebug) {
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

        $prefix = trim($prefix);

        if ('' !== $prefix) {
            if ('\\' !== substr($prefix, -1, 1)) {
                throw new \InvalidArgumentException('A non-empty PSR-4 prefix must end with a namespace separator');
            }
        }
        if (!is_array($baseDir)) {
            throw new \InvalidArgumentException("A array was required instead of $baseDir");
        }
        foreach ($baseDir as $dir) {
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
        $subPath = $logicPath;
        while (false !== ($pos = strrpos($subPath, '/'))) {
            $subPath = substr($subPath, 0, $pos);
            $prefix = substr($classname, 0, $pos + 1);
            if (isset($this->prefixDir[$prefix])) {
                $endPath = substr($logicPath, $pos + 1);
                foreach ($this->prefixDir[$prefix] as $baseDir) {
                    $file = $baseDir . '/' . $endPath;
                    if (is_file($file)) {
                        return $file;
                    }
                }
            }
        }
        
        foreach ($this->fallbackDir as $baseDir) {
            $file = $baseDir . '/' . $logicPath;
            if (is_file($file)) {
                return $file;
            }
        }
        return false;
    }
}
