<?php


namespace Best;


use Best\Foundation\Http\Uri;
use Best\Foundation\Http\CgiRequest;

class Request extends CgiRequest
{
    /**
     * CgiRequest constructor.
     *
     * @param $server
     * @param $cookie
     * @param $get
     * @param $post
     * @param $files
     */
    public function __construct($server = [], $cookie = [], $get = [], $post = [], $files = [])
    {
        $server = $server + $_SERVER;
        $cookie = $cookie + $_COOKIE;
        $get = $get + $_GET;
        $post = $post + $_POST;
        $files = $files + $_FILES;

        $this->withServer($server)
            ->withCookie($cookie)
            ->withGet($get)
            ->withPost($post)
            ->withFiles($this->uploadedFileFactory($files))
            ->withInput();
    }

    /**
     * Get the request uri, composed of path, query.
     *
     * @return Uri
     */
    public function getUri()
    {
        $requestTarget = trim($this->server('REQUEST_URI'));

        return (new Uri($requestTarget));
    }
    
    public function withUri(string $uri)
    {
        $this->withServer('REQUEST_URI', $uri);
        return $this;
    }
    
    public function getPathinfo()
    {
        $path = rtrim($this->getUri()->getPath(), '/');
        $pathinfo = preg_replace('#\w+\.php#', '', $path);

        return $pathinfo;
    }

    public function getMethod()
    {
        return $this->server('REQUEST_METHOD') ?? 'GET';
    }

    public function withMethod(string $method)
    {
        $this->withServer('REQUEST_METHOD', $method);
        return $this;
    }
}