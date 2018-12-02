<?php


namespace Best;


class Loader
{
    protected $isDebug;
    protected $loaders = [];

    /**
     * The configure directory of autoload file
     *
     * @var string
     */
    protected $path = '';

    public function __construct($path, bool $isDebug = false)
    {
        $this->path = $path;
        $this->isDebug = $isDebug;
    }

    public function addLoader($loaderName)
    {
        if (is_array($loaderName)) {
            foreach ($loaderName as $value) {
                $this->loaders[] = $value;
            }
            return;
        }

        $this->loaders[] = $loaderName;
    }

    public function register()
    {
        include __DIR__ . '/Contract/Loader/LoaderInterface.php';
        foreach ($this->loaders as $loader) {
            $config = include($this->path . '/' . strtolower($loader) . '.php');
            include __DIR__ . '/Driver/Loader/' . $loader . '.php';
            $loader = '\\Best\\Driver\\Loader\\' . $loader;
            $loader = new $loader($this->isDebug);
            $loader->add($config);
            $loader->register();
        }
    }
}
