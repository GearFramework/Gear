<?php

namespace Gear\Traits\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * HTTP messages consist of requests from a client to a server and responses
 * from a server to a client. This interface defines the methods common to
 * each.
 *
 * Messages are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
trait TMessage
{
    protected $_protocolVersion = '1.1';
    protected $_headers = [];
    protected $_body = null;

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getProtocolVersion(): string
    {
        return $this->_protocolVersion;
    }

    /**
     * Set the HTTP protocol version as a string.
     *
     * @param string $protocolVersion
     * @return MessageInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setProtocolVersion(string $protocolVersion): MessageInterface
    {
        $this->_protocolVersion = $protocolVersion;
        return $this;
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     * @return MessageInterface|static
     * @since 0.0.1
     * @version 0.0.1
     */
    public function withProtocolVersion(string $version): MessageInterface
    {
        $message = $this;
        if ($version !== $this->getProtocolVersion()) {
            $message = clone $this;
            $message->_protocolVersion = $version;
        }
        return $message;
    }

    /**
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getHeaders(): array
    {
        return $this->_headers;
    }

    /**
     * Set array with all message header values.
     *
     * @param array $headers
     * @return MessageInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setHeaders(array $headers): MessageInterface
    {
        $this->_headers = $headers;
        return $this;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->_headers[strtolower($name)]);
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getHeader(string $name): array
    {
        $header = [];
        if ($this->hasHeader($name)) {
            $header = $this->_headers[strtolower($name)];
        }
        return $header;
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function withHeader(string $name, $value): MessageInterface
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        $name = strtolower($name);
        $message = clone $this;
        foreach ($value as $v) {
            $message->_headers[$name][] = trim($v, " \t");
        }
        return $message;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function withAddedHeader(string $name, $value): MessageInterface
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        $name = strtolower($name);
        foreach ($value as &$v) {
            $v = trim($v, " \t");
        }
        unset($v);
        $message = clone $this;
        if (isset($message->_headers[$name])) {
            $message->_headers[$name] = array_merge($this->_headers[$name], $value);
        } else {
            $message->_headers[$name] = $value;
        }
        return $message;
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return static
     * @since 0.0.1
     * @version 0.0.1
     */
    public function withoutHeader(string $name): MessageInterface
    {
        $message = $this;
        if ($this->hasHeader($name)) {
            $message = clone $this;
            unset($message->_headers[strtolower($name)]);
        }
        return $message;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getBody(): StreamInterface
    {
        return $this->_body;
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return static
     * @throws \InvalidArgumentException When the body is not valid.
     * @since 0.0.1
     * @version 0.0.1
     */
    public function withBody(StreamInterface $body): MessageInterface
    {
        $message = $this;
        if ($body !== $this->_body) {
            $message = clone $this;
            $message->_body = $body;
        }
        return $message;
    }
}
