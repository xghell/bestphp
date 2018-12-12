<?php


namespace Best\Http\Foundation;


class Uri
{
    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $fragment;

    /**
     * Uri constructor.
     *
     * @param string $uri
     */
    public function __construct(string $uri = '')
    {
        $info = parse_url($uri);
        $this->withScheme($info['scheme'] ?? null)
            ->withUserInfo($info['user'] ?? null, $info['pass'] ?? null)
            ->withHost($info['host'] ?? null)
            ->withPort($info['port'] ?? null)
            ->withPath($info['path'] ?? null)
            ->withQuery($info['query'] ?? null)
            ->withFragment($info['fragment'] ?? null);
    }

    /**
     * Retrieve the scheme component of the URI.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @return string The URI scheme.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string|null The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        $user = $this->getUser();

        $password = $this->getPassword();
        $password = !is_null($password) ? ':' . $password : null;

        if (is_null($user)) {
            return null;
        }

        $userInfo = $user . $password;

        return '' !== trim($userInfo) ? $userInfo : null;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * @return string|null The URI host.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retrieve the port component of the URI.
     *
     * @return null|int The URI port.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * @return string|null The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        $userInfo = $this->getUserInfo();
        $userInfo = !is_null($userInfo) ? $userInfo . '@' : null;

        $host = $this->getHost();

        $port = $this->getPort();
        $port = !is_null($port) ? ':' . $port : null;

        $authority = $userInfo . $host . $port;

        return '' !== trim($authority) ? $authority : null;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * @return string|null The URI path.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @return string|null The URI query string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters.
     *
     * @return string|null The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Return an instance with the specified scheme.
     *
     * @param string $scheme
     * @return $this|UriInterface
     */
    public function withScheme($scheme)
    {
        $this->scheme = strtolower($scheme);
        return $this;
    }

    /**
     * Return an instance with the specified userInfo.
     *
     * @param string $user
     * @param null $password
     * @return $this|UriInterface
     */
    public function withUserInfo($user, $password = null)
    {
        $this->user = $user;
        $this->password = $password;
        return $this;
    }

    /**
     * Return an instance with the specified host.
     *
     * @param string $host
     * @return $this|UriInterface
     */
    public function withHost($host)
    {
        $this->host = strtolower($host);
        return $this;
    }

    /**
     * Return an instance with the specified port.
     *
     * @param int|null $port
     * @return $this|UriInterface
     */
    public function withPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Return an instance with the specified path.
     *
     * @param string $path
     * @return $this|UriInterface
     */
    public function withPath($path)
    {
        $this->path = '/' . trim($path, '/');
        return $this;
    }

    /**
     * Return an instance with the specified query string.
     *
     * @param string $query
     * @return $this|UriInterface
     */
    public function withQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * @param string $fragment
     * @return $this|UriInterface
     */
    public function withFragment($fragment)
    {
        $this->fragment = $fragment;
        return $this;
    }

    /**
     * Return the string representation as a URI reference.
     * (e.g., http://username:password@hostname/path?arg=value#anchor)
     *
     * The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @return string|null
     */
    public function __toString()
    {
        $scheme = $this->getScheme();
        $scheme = !is_null($scheme) ? $scheme . ':' : null;

        $authority = $this->getAuthority();
        $authority = !is_null($authority) ? '//' . $authority : null;

        $path = urlencode($this->getPath());
        
        $query = $this->getQuery();
        $query = !is_null($query) ? urlencode('?' . $query) : null;
        
        $fragment = $this->getFragment();
        $fragment = !is_null($fragment) ? urlencode('#' . $fragment) : null;

        $uri = $scheme . $authority . $path . $query . $fragment;
        return '' !== trim($uri) ? $uri : null;
    }
}