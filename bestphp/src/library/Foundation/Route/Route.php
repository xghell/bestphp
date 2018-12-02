<?php


namespace Best\Foundation\Route;


class Route extends Rule
{
    /**
     * The route group name
     *
     * @var string
     */
    private $group;

    /**
     * @var 'user/{name}/{age}'
     */
    private $path;

    /**
     * @var Response|\Closure|string
     */
    private $action;

    /**
     * Route constructor.
     *
     * @param string $path
     * @param string|\Closure|Response $action
     */
    public function __construct(string $path, $action)
    {
        $this->withPath($path)
            ->withAction($action);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function withPath(string $path)
    {
        $this->path = '/' . trim($path, '/');
        return $this;
    }

    /**
     * @return Response|\Closure|string
     */
    public function getAction()
    {
        return $this->action;
    }

    public function withAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function group($groupName)
    {
        $this->group = $groupName;
        return $this;
    }

    public function getGroup()
    {
        return $this->group;
    }
    
}