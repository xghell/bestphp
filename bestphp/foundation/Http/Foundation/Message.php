<?php


namespace Best\Http\Foundation;


class Message
{
    /**
     * @var string
     */
    private $protocol;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var Stream|string
     */
    private $body;

    /**
     * Retrieves the HTTP protocol as a string.
     *
     * (e.g., "HTTP/1.1", "HTTP/1.0").
     *
     * @return string HTTP protocol.
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Return an instance with the specified HTTP protocol.
     * (e.g., "HTTP/1.1", "HTTP/1.0").
     *
     * @param string $protocol
     * @return $this|MessageInterface
     */
    public function withProtocol($protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * Retrieves all message header values.
     * @return array|\string[][]   Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        $name = strtolower($name);
        return isset($this->headers[$name]);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header.
     */
    public function getHeader($name)
    {
        $name = strtolower($name);
        return $this->headers[$name] ?? null;
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     * cookie is separated by a ';'
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a ',' or ';'.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a ',' or ';'.
     */
    public function getHeaderLine($name)
    {
        $name = strtolower($name);
        $separator = 'cookie' === $name ? ';' : ',';
        $header = $this->getHeader($name);
        $header = implode($separator, $header);

        return $header;
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * @param string $name   Case-insensitive header field name.
     * @param string|string[] $value
     * @return $this|MessageInterface
     */
    public function withHeader($name, $value)
    {
        $name = strtolower($name);
        $separator = 'cookie' === $name ? ';' : ',';
        if (is_string($value)) {
            $value = explode($separator, $value);
        }

        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * @param string $name
     * @param string|string[] $value
     * @return $this|MessageInterface
     */
    public function withAddedHeader($name, $value)
    {
        $name = strtolower($name);
        $separator = 'cookie' === $name ? ';' : ',';
        if (is_string($value)) {
            $value = explode($separator, $value);
        }

        if ($this->hasHeader($name)) {
            $this->headers[$name] = array_merge($this->headers[$name], $value);
        } else {
            $this->headers[$name] = $value;
        }

        return $this;
    }

    /**
     * Return an instance without the specified header.
     *
     * @param string $name
     * @return MessageInterface|void
     */
    public function withoutHeader($name)
    {
        if ($this->hasHeader($name)) {
            unset($this->headers[$name]);
        }
    }

    /**
     * Gets the body of the message.
     *
     * @return Stream|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a Stream object.
     *
     * @param Stream|string $body
     * @return $this|MessageInterface
     */
    public function withBody($body)
    {
        $this->body = $body;
        return $this;
    }
}