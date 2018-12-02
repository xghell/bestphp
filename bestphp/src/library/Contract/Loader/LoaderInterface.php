<?php


namespace Best\Contract\Loader;


interface LoaderInterface
{
    public function __construct(bool $isDebug = false);

    public function register();

    public function add($name, $path, bool $prepend = false);
}