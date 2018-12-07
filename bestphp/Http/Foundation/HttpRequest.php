<?php


namespace Best\Http\Foundation;


class HttpRequest extends Message
{
    /**
     * @var string (e.g., GET, POST, PUT, DELETE, OPTIONS, HEADER, PATCH)
     */
    private $method;

    /**
     * @var string
     */
    private $requestTarget;

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     * 
     * @param string $method  Case-sensitive method.
     * @return $this|RequestInterface
     */
    public function withMethod($method)
    {
        $this->method = $method;
        return $this;
    }
    
    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        return $this->requestTarget ?? '/';
    }

    /**
     * Return an instance with the specific request-target.
     * 
     * @param mixed $requestTarget
     * @return $this|RequestInterface
     */
    public function withRequestTarget($requestTarget)
    {
        $this->requestTarget = $requestTarget;
        return $this;
    }
}