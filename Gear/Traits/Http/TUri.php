<?php

namespace Gear\Traits\Http;

use Psr\Http\Message\UriInterface;

trait TUri
{
    /**
     * @var string $_scheme Uri scheme (without "://" suffix)
     */
    protected $_scheme = null;
    /**
     * @var string $_host Uri host
     */
    protected $_host = null;
    protected $_port = null;
    protected $_user = null;
    protected $_pass = null;
    protected $_path = null;
    protected $_query = null;
    protected $_fragment = null;
    protected $_userInfo = null;

    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
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
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __toString(): string
    {
        return $this->compileUri();
    }

    public function getUri(): string
    {
        return $this->_uri;
    }

    public function setUri(string $uri)
    {
        $this->_uri = $uri;
    }

    /**
     * Retrieve the scheme component of the URI.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getScheme(): string
    {
        return $this->_scheme;
    }

    /**
     * Return an instance with the specified scheme.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     *
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return static A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function withScheme(string $scheme): UriInterface
    {
        $uri = $this;
        if ($scheme !== $this->getScheme()) {
            $uri = clone $this;
            $uri->_scheme = $scheme;
        }
        return $uri;
    }

    public function withoutScheme(): UriInterface
    {
        $uri = $this;
        if ($this->getScheme()) {
            $uri = clone $this;
            $uri->_scheme = null;
        }
        return $uri;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAuthority(): string
    {
        $authority = $this->getHost();
        if (($userInfo = $this->getUserInfo())) {
            $authority = $userInfo . '@' . $authority;
        }
        $port = $this->getPort();
        $authority .= $port ? ':' . $port : '';
        return $authority;
    }

    public function getUser(): string
    {
        return $this->_user;
    }

    public function withUser(string $user): UriInterface
    {
        $uri = $this;
        if ($user !== $this->getUser()) {
            $uri = clone $this;
            $uri->_user = $user;
        }
        return $uri;
    }

    public function withoutUser(): UriInterface
    {
        $uri = $this;
        if ($this->getUser()) {
            $uri = clone $this;
            $uri->_user = null;
            $uri->_pass = null;
        }
        return $uri;
    }

    public function getPass(): string
    {
        return $this->_pass;
    }

    public function withPass(string $pass): UriInterface
    {
        $uri = $this;
        if ($pass !== $this->getPass() && $this->getUser()) {
            $uri = clone $this;
            $uri->_pass = $pass;
        }
        return $uri;
    }

    public function withoutPass(): UriInterface
    {
        $uri = $this;
        if ($this->getPass()) {
            $uri = clone $this;
            $uri->_pass = null;
        }
        return $uri;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string The URI user information, in "username[:password]" format.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getUserInfo(): string
    {
        return $this->_userInfo;
    }

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return static A new instance with the specified user information.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function withUserInfo(string $user, string $password = null): UriInterface
    {
        $uri = $this;
        $userInfo = $user . ($password ? ':' . $password : '');
        if ($userInfo !== $this->getUserInfo()) {
            $uri = clone $this;
            $uri->_user = $user;
            $uri->_pass = $password;
            $uri->_userInfo = $userInfo;
        }
        return $uri;
    }

    public function withoutUserInfo(): UriInterface
    {
        $uri = $this;
        if ($this->getUserInfo()) {
            $uri = clone $this;
            $uri->_userInfo = null;
            $uri->_user = null;
            $uri->_pass = null;
        }
        return $uri;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * If no host is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getHost(): string
    {
        return $this->_host;
    }

    /**
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     * @return static A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function withHost(string $host): UriInterface
    {
        $uri = $this;
        if ($host !== $this->getHost()) {
            $uri = clone $this;
            $uri->_host = $host;
        }
        return $uri;
    }

    /**
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPort()
    {
        $this->_port;
    }

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return static A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function withPort(int $port): UriInterface
    {
        $uri = $this;
        if ($port !== $this->getPort()) {
            $uri = clone $this;
            $uri->_port = $port;
        }
        return $uri;
    }

    public function withoutPort(): UriInterface
    {
        $uri = $this;
        if ($this->getPort()) {
            $uri = clone $this;
            $uri->_port = null;
        }
        return $uri;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * Normally, the empty path "" and absolute path "/" are considered equal as
     * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
     * do this normalization because in contexts with a trimmed base path, e.g.
     * the front controller, this difference becomes significant. It's the task
     * of the user to handle both "" and "/".
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.3.
     *
     * As an example, if the value should include a slash ("/") not intended as
     * delimiter between path segments, that value MUST be passed in encoded
     * form (e.g., "%2F") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPath(): string
    {
        return $this->_path;
    }

    /**
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     * @return static A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function withPath(string $path): UriInterface
    {
        $uri = $this;
        if ($path !== $this->getPath()) {
            $uri = clone $this;
            $uri->_path = $path;
        }
        return $uri;
    }

    public function withoutPath(): UriInterface
    {
        $uri = $this;
        if ($this->getPath()) {
            $uri = clone $this;
            $uri->_path = null;
        }
        return $uri;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getQuery(): string
    {
        return $this->_query;
    }

    /**
     * Return an instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     *
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in getQuery().
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return static A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function withQuery(string $query): UriInterface
    {
        if (is_array($query)) {
            $temp = [];
            foreach ($query as $name => $value) {
                $temp[] = $name . '=' . urlencode($value);
            }
            $query = implode('&', $temp);
        } else if (is_object($query) && method_exists($query, '__toString')) {
            $query = (string)$query;
        } else {
            throw Core::exceptionCore('Invalid query uri {query}', ['query' => $query]);
        }
        $uri = $this;
        if ($query !== $this->getQuery()) {
            $uri = clone $this;
            $uri->_query = $query;
        }
        return $uri;
    }

    public function withoutQuery(): UriInterface
    {
        $uri = $this;
        if ($this->getQuery()) {
            $uri = clone $this;
            $uri->_query = null;
        }
        return $uri;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getFragment(): string
    {
        return $this->_fragment;
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified URI fragment.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return static A new instance with the specified fragment.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function withFragment(string $fragment): UriInterface
    {
        $uri = $this;
        if ($fragment !== $this->getFragment()) {
            $uri = clone $this;
            $uri->_fragment = $fragment;
        }
        return $uri;
    }

    public function withoutFragment(): UriInterface
    {
        $uri = $this;
        if ($this->getFragment()) {
            $uri = clone $this;
            $uri->_fragment = null;
        }
        return $uri;
    }

    public function compileUri(): string
    {
        $uri = $this->getAuthority();
        if ($scheme = $this->getScheme())
            $uri = $scheme . '://' . $uri;
        if ($path = $this->getPath())
            $uri .= '/' . $path;
        if ($query = $this->getQuery())
            $uri .= '?' . $query;
        if ($fragment = $this->getFragment())
            $uri .= '#' . $fragment;
        return $uri;
    }
}
