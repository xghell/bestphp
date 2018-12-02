<?php


namespace Best\Foundation\Route;


class RouteGroup extends Rule
{
    public function __construct($name)
    {
        $this->name($name);
    }
}