<?php


namespace Best\Autoload;


class Loader
{
    /**
     * Determine if debug model.
     *
     * @var bool
     */
    protected $appDebug;

    /**
     * @var \Best\Best\Autoload\Contract\Loader[]
     */
    protected $loaders = [];

    /**
     * The configure directory of autoload file
     *
     * @var string
     */
    protected $path;

    /**
     * The configure directory of composer autoload file
     *
     * @var string
     */
    protected $composerPath;

    /**
     * Loader constructor.
     *
     * @param string $path
     * @param string $composerPath
     * @param bool $appDebug
     */
    public function __construct(string $path, string $composerPath = '', bool $appDebug = false)
    {
        $this->path = $path;
        $this->composerPath = $composerPath;
        $this->appDebug = $appDebug;
    }

    /**
     * The loader name implement \Best\autoload\Contract\Loader.
     *
     * @param $loaderName  Case-Sensitive.
     */
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

    /**
     * Register autoload.
     *
     * Support composer autoload.
     */
    public function register()
    {
        include __DIR__ . '/Contract/Loader.php';

        foreach ($this->loaders as $loader) {
            $userConfig = include $this->path . '/' . strtolower($loader) . '.php';
            $composerConfig = [];

            switch ($loader) {
                case 'Psr4':
                    if (is_file($this->composerPath . '/autoload_psr4.php')) {
                        $composerConfig = include $this->composerPath . '/autoload_psr4.php';
                    }
                    break;
                case 'Psr0':
                    if (is_file($this->composerPath . '/autoload_namespaces.php')) {
                        $composerConfig = include $this->composerPath . '/autoload_namespaces.php';
                    }
                    break;
                case 'ClassMap':
                    if (is_file($this->composerPath . '/autoload_classmap.php')) {
                        $composerConfig = include $this->composerPath . '/autoload_classmap.php';
                    }
                    break;
                case 'File':
                    if (is_file($this->composerPath . '/autoload_file.php')) {
                        $composerConfig = include $this->composerPath . '/autoload_file.php';
                    }
                    break;
            }

            $config = $userConfig + $composerConfig;

            include __DIR__ . '/Driver/' . $loader . '.php';
            $loader = 'Best\Autoload\\Driver\\' . $loader;

            $loader = new $loader($this->appDebug);
            $loader->add($config);
            $loader->register();
        }
    }
}
