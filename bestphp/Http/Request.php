<?php


namespace Best\Http;


use Best\Http\Foundation\Uri;
use Best\Http\Foundation\CgiRequest;

class Request extends CgiRequest
{
    /**
     * Request constructor.
     *
     * @param array $server
     * @param array $cookie
     * @param array $get
     * @param array $post
     * @param array $files
     * @throws \ReflectionException
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

    /**
     * Set the request uri
     *
     * @param string $uri
     * @return $this
     */
    public function withUri(string $uri)
    {
        $this->withServer('REQUEST_URI', $uri);
        return $this;
    }

    /**
     * The path info
     *
     * @return string|string[]|null
     */
    public function getPathInfo()
    {
        $path = rtrim($this->getUri()->getPath(), '/');
        $pathInfo = preg_replace('#/?\w+\.php#', '', $path);

        return $pathInfo;
    }

    /**
     * Get the request method
     *
     * @return array|mixed|string|null
     */
    public function getMethod()
    {
        return $this->server('REQUEST_METHOD') ?? 'GET';
    }

    /**
     * Set the request method.
     *
     * @param string $method
     * @return $this
     */
    public function withMethod(string $method)
    {
        $this->withServer('REQUEST_METHOD', $method);
        return $this;
    }
}