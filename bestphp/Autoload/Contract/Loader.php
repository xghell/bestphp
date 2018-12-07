<?php


namespace Best\Autoload\Contract;


abstract class Loader
{
    /**
     * Determine if debug model
     *
     * @var bool
     */
    protected $appDebug;
    
    /**
     * LoaderInterface constructor.
     *
     * @param bool $appDebug
     */
    public function __construct(bool $appDebug = false)
    {
        $this->appDebug = $appDebug;
    }

    /**
     * @return bool
     */
    protected function isDebug()
    {
        return $this->appDebug;
    }

    /**
     * Register autoload
     *
     * @return mixed
     */
    abstract public function register();

    /**
     * Add autoload rule
     *
     * @param $name
     * @param $path
     * @param bool $prepend
     * @return mixed
     */
    abstract public function add($name, $path, bool $prepend = false);

    /**
     * If debug model, Check the params of the function 'add'.
     *
     * @param $name
     * @param $path
     * @return mixed
     */
    abstract protected function checkParam($name, $path);
}