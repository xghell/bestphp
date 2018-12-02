<?php


namespace Best\Driver\Loader;


use Best\Contract\Loader\LoaderInterface;

class File implements LoaderInterface
{
    /**
     * Debug mode or not
     *
     * @var bool
     */
    protected $isDebug = false;

    /**
     * The file that need to be loaded automatically
     *
     * @var array
     */
    protected $files = [];

    /**
     * Register autoload service
     */
    public function register()
    {
        $this->loadFile();
    }

    /**
     * File constructor.
     *
     * @param bool $isDebug
     */
    public function __construct(bool $isDebug = false)
    {
        $this->isDebug = $isDebug;
    }

    /**
     * Add the file that need to be loaded automatically
     *
     * @param string|array $fileIdentifier
     * @param string $file
     * @param bool $prepend
     */
    public function add($fileIdentifier, $file = '', bool $prepend = false)
    {
        if (is_array($fileIdentifier)) {
            if ($prepend) {
                $this->files = array_merge($fileIdentifier, $this->files);
            } else {
                $this->files = array_merge($this->files, $fileIdentifier);
            }
            return;
        }

        if ($prepend) {
            $this->files[$fileIdentifier] = $file;
        } else {
            if (!isset($this->files[$fileIdentifier])) {
                $this->files[$fileIdentifier] = $file;
            }
        }
    }

    /**
     * Check the parameter of the function 'add'
     *
     * @param string|array $fileIdentifier
     * @param string $file
     */
    protected function checkParam($fileIdentifier, $file = '')
    {
        if (!$this->isDebug) {
            return;
        }

        if (is_array($fileIdentifier)) {
            foreach ($fileIdentifier as $value) {
                $this->checkParam($value);
            }
            return;
        }

        if (!is_file($file)) {
            throw new \InvalidArgumentException("File($file) not exists.");
        }
    }

    /**
     * Load all of registered files automatically
     */
    protected function loadFile()
    {
        foreach ($this->files as $file) {
            if (is_file($file)) {
                include $file;
            }
        }
    }
}
