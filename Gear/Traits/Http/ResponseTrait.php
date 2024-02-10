<?php

namespace Gear\Traits\Http;

use Gear\Interfaces\Http\ResponseInterface;

/**
 * Representation of an outgoing, server-side response.
 *
 * Per the HTTP specification, this interface includes properties for
 * each of the following:
 *
 * - Protocol version
 * - Status code and reason phrase
 * - Headers
 * - Message body
 *
 * Responses are considered immutable; all methods that might change state MUST
 * be implemented such that they retain the internal state of the current
 * message and return an instance that contains the changed state.
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
trait ResponseTrait
{
    protected int $statusCode = ResponseInterface::DEFAULT_STATUS_CODE;
    protected string $reasonPhrase = ResponseInterface::HTTP_STATUS_PHRASES[ResponseInterface::DEFAULT_STATUS_CODE];

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode(): int 
    {
        return $this->statusCode;
    }

    /**
     * Sets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @param   int $status
     * @return  ResponseInterface
     */
    public function setStatusCode(int $status): ResponseInterface
    {
        $this->statusCode = $status;
        /** @var ResponseInterface $this */
        return $this;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param   int     $code The 3-digit integer result code to set.
     * @param   string  $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return  ResponseInterface
     */
    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        if ($code !== $this->statusCode || $reasonPhrase !== $this->reasonPhrase) {
            /** @var ResponseInterface $response */
            $response = clone $this;
            $response->setStatusCode($code);
            if ($reasonPhrase === '' && isset(ResponseInterface::HTTP_STATUS_PHRASES[$code])) {
                $reasonPhrase = ResponseInterface::HTTP_STATUS_PHRASES[$code];
            }
            $response->setReasonPhrase($reasonPhrase);
            return $response;
        }
        /** @var ResponseInterface $this */
        return $this;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase(): string 
    {
        return $this->reasonPhrase;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param   string $phrase
     * @return  ResponseInterface
     */
    public function setReasonPhrase(string $phrase): ResponseInterface
    {
        $this->reasonPhrase = $phrase;
        /** @var ResponseInterface $this */
        return $this;
    }
}
