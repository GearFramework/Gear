<?php

namespace Gear\Interfaces\Http;

use Gear\Interfaces\ApplicationInterface;
use Gear\Interfaces\Services\PluginInterface;

/**
 * Интерфейс плагина для работы с ответами на пользовательские запросы
 *
 * @property ApplicationInterface owner
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface ResponseInterface extends PluginInterface
{
    const DEFAULT_STATUS_CODE = HttpInterface::HTTP_STATUS_OK;
    const HTTP_STATUS_PHRASES = [
        HttpInterface::HTTP_STATUS_CONTINUE                         => 'Continue',
        HttpInterface::HTTP_STATUS_SWITCH_PROTOCOLS                 => 'Switching Protocols',
        HttpInterface::HTTP_STATUS_PROCESSING                       => 'Processing',
        HttpInterface::HTTP_STATUS_OK                               => 'OK',
        HttpInterface::HTTP_STATUS_CREATED                          => 'Created',
        HttpInterface::HTTP_STATUS_ACCEPTED                         => 'Accepted',
        HttpInterface::HTTP_STATUS_AUTHORITATIVE_INFORMATION        => 'Non-Authoritative Information',
        HttpInterface::HTTP_STATUS_NO_CONTENT                       => 'No Content',
        HttpInterface::HTTP_STATUS_RESET_CONTENT                    => 'Reset Content',
        HttpInterface::HTTP_STATUS_PARTIAL_CONTENT                  => 'Partial Content',
        HttpInterface::HTTP_STATUS_MULTI_STATUS                     => 'Multi-Status',
        HttpInterface::HTTP_STATUS_ALREADY_REPORTED                 => 'Already Reported',
        HttpInterface::HTTP_STATUS_IM_USED                          => 'IM Used',
        HttpInterface::HTTP_STATUS_MULTIPLE_CHOICES                 => 'Multiple Choices',
        HttpInterface::HTTP_STATUS_MOVED_PERMANENTLY                => 'Moved Permanently',
        HttpInterface::HTTP_STATUS_FOUND                            => 'Found',
        HttpInterface::HTTP_STATUS_SEE_OTHER                        => 'See Other',
        HttpInterface::HTTP_STATUS_NOT_MODIFIED                     => 'Not Modified',
        HttpInterface::HTTP_STATUS_USE_PROXY                        => 'Use Proxy',
        HttpInterface::HTTP_STATUS_UNUSED                           => '(Unused)',
        HttpInterface::HTTP_STATUS_TEMPORARY_REDIRECT               => 'Temporary Redirect',
        HttpInterface::HTTP_STATUS_PERMANENT_REDIRECT               => 'Permanent Redirect',
        HttpInterface::HTTP_STATUS_BAD_REQUEST                      => 'Bad Request',
        HttpInterface::HTTP_STATUS_UNAUTHORIZED                     => 'Unauthorized',
        HttpInterface::HTTP_STATUS_PAYMENT_REQUIRED                 => 'Payment Required',
        HttpInterface::HTTP_STATUS_FORBIDDEN                        => 'Forbidden',
        HttpInterface::HTTP_STATUS_NOT_FOUND                        => 'Not Found',
        HttpInterface::HTTP_STATUS_METHOD_NOT_ALLOWED               => 'Method Not Allowed',
        HttpInterface::HTTP_STATUS_NOT_ACCEPTABLE                   => 'Not Acceptable',
        HttpInterface::HTTP_STATUS_PROXY_AUTHENTICATION_REQUIRED    => 'Proxy Authentication Required',
        HttpInterface::HTTP_STATUS_REQUEST_TIMEOUT                  => 'Request Timeout',
        HttpInterface::HTTP_STATUS_CONFLICT                         => 'Conflict',
        HttpInterface::HTTP_STATUS_GONE                             => 'Gone',
        HttpInterface::HTTP_STATUS_LENGTH_REQUIRED                  => 'Length Required',
        HttpInterface::HTTP_STATUS_PRECONDITION_FAILED              => 'Precondition Failed',
        HttpInterface::HTTP_STATUS_TOO_LARGE                        => 'Request Entity Too Large',
        HttpInterface::HTTP_STATUS_TOO_LONG                         => 'Request-URI Too Long',
        HttpInterface::HTTP_STATUS_UNSUPPORTED_MEDIA_TYPE           => 'Unsupported Media Type',
        HttpInterface::HTTP_STATUS_RANGE_NOT_SATISFIABLE            => 'Requested Range Not Satisfiable',
        HttpInterface::HTTP_STATUS_EXPECTATION_FAILED               => 'Expectation Failed',
        HttpInterface::HTTP_STATUS_I_TEAPOT                         => 'I\'m a teapot',
        HttpInterface::HTTP_STATUS_UNPROCESSABLE_ENTITY             => 'Unprocessable Entity',
        HttpInterface::HTTP_STATUS_LOCKED                           => 'Locked',
        HttpInterface::HTTP_STATUS_FAILED_DEPENDENCY                => 'Failed Dependency',
        HttpInterface::HTTP_STATUS_UPGRADE_REQUIRED                 => 'Upgrade Required',
        HttpInterface::HTTP_STATUS_PRECONDITION_REQUIRED            => 'Precondition Required',
        HttpInterface::HTTP_STATUS_TOO_MANY_REQUESTS                => 'Too Many Requests',
        HttpInterface::HTTP_STATUS_HEADER_TOO_LARGE                 => 'Request Header Fields Too Large',
        HttpInterface::HTTP_STATUS_UNAVAILABLE_LEGAL_REASONS        => 'Unavailable For Legal Reasons',
        HttpInterface::HTTP_STATUS_INTERNAL_SERVER_ERROR            => 'Internal Server Error',
        HttpInterface::HTTP_STATUS_NOT_IMPLEMENTED                  => 'Not Implemented',
        HttpInterface::HTTP_STATUS_BAD_GATEWAY                      => 'Bad Gateway',
        HttpInterface::HTTP_STATUS_SERVICE_UNAVAILABLE              => 'Service Unavailable',
        HttpInterface::HTTP_STATUS_GATEWAY_TIMEOUT                  => 'Gateway Timeout',
        HttpInterface::HTTP_STATUS_VERSION_NOT_SUPPORTED            => 'HTTP Version Not Supported',
        HttpInterface::HTTP_STATUS_VARIANT_VERSO_NEGOTIATES         => 'Variant Also Negotiates',
        HttpInterface::HTTP_STATUS_INSUFFICIENT_STORAGE             => 'Insufficient Storage',
        HttpInterface::HTTP_STATUS_LOOP_DETECTED                    => 'Loop Detected',
        HttpInterface::HTTP_STATUS_NOT_EXTENDED                     => 'Not Extended',
        HttpInterface::HTTP_STATUS_NETWORK_AUTHENTICATION           => 'Network Authentication Required',
    ];

    /**
     * Sets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @param int $status
     * @return ResponseInterface
     */
    public function setStatusCode(int $status): ResponseInterface;

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
     * @param string $phrase
     * @return ResponseInterface
     */
    public function setReasonPhrase(string $phrase): ResponseInterface;
}