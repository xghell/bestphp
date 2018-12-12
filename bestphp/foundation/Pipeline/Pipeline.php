<?php


namespace Best\Pipeline;


class Pipeline
{
    /**
     * @var \Closure[]
     */
    private $handlers;
    
    public function send($value)
    {
        return array_reduce($this->handlers, function ($carry, $item) {
            return call_user_func($item, $carry);
        }, $value);
    }
    
    public function through(array $handlers)
    {
        $this->handlers = $handlers;
        return $this;
    }
}