<?php


namespace Best\Route\Dispatch;


use Best\Container;
use Best\Route\Dispatch\Contract\Dispatch;

class Callback implements Dispatch
{
    /**
     * @var \Closure
     */
    private $closure;

    /**
     * @var array
     */
    private $arguments;

    /**
     * Callback constructor.
     *
     * @param $action
     * @param array $vars
     */
    public function __construct($action, array $vars)
    {
        $this->closure = $action;
        $this->arguments = $vars;
    }

    /**
     * Execute the dispatch action, return the http response message
     *
     * @return mixed
     */
    public function execute()
    {
        return Container::getInstance()->invoke($this->closure, $this->arguments);
    }
}