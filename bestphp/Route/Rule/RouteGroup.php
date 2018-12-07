<?php


namespace Best\Route\Rule;


class RouteGroup extends Rule
{
    public function __construct($name)
    {
        $this->name($name);
    }
}