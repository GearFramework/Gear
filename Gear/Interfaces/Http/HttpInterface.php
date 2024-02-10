<?php

namespace Gear\Interfaces\Http;

/**
 * HTTP-интерфейс
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface HttpInterface
{
    /* Методы запросов */
    const CLI                                       = 'CLI';
    const GET                                       = 'GET';
    const POST                                      = 'POST';
    const PUT                                       = 'PUT';
    const DELETE                                    = 'DELETE';
    const FILES                                     = 'FILES';
    /* Http статусы*/
    const HTTP_STATUS_CONTINUE                      = 100;
    const HTTP_STATUS_SWITCH_PROTOCOLS              = 101;
    const HTTP_STATUS_PROCESSING                    = 102;
    const HTTP_STATUS_OK                            = 200;
    const HTTP_STATUS_CREATED                       = 201;
    const HTTP_STATUS_ACCEPTED                      = 202;
    const HTTP_STATUS_AUTHORITATIVE_INFORMATION     = 203;
    const HTTP_STATUS_NO_CONTENT                    = 204;
    const HTTP_STATUS_RESET_CONTENT                 = 205;
    const HTTP_STATUS_PARTIAL_CONTENT               = 206;
    const HTTP_STATUS_MULTI_STATUS                  = 207;
    const HTTP_STATUS_ALREADY_REPORTED              = 208;
    const HTTP_STATUS_IM_USED                       = 226;
    const HTTP_STATUS_MULTIPLE_CHOICES              = 300;
    const HTTP_STATUS_MOVED_PERMANENTLY             = 301;
    const HTTP_STATUS_FOUND                         = 302;
    const HTTP_STATUS_SEE_OTHER                     = 303;
    const HTTP_STATUS_NOT_MODIFIED                  = 304;
    const HTTP_STATUS_USE_PROXY                     = 305;
    const HTTP_STATUS_UNUSED                        = 306;
    const HTTP_STATUS_TEMPORARY_REDIRECT            = 307;
    const HTTP_STATUS_PERMANENT_REDIRECT            = 308;
    const HTTP_STATUS_BAD_REQUEST                   = 400;
    const HTTP_STATUS_UNAUTHORIZED                  = 401;
    const HTTP_STATUS_PAYMENT_REQUIRED              = 402;
    const HTTP_STATUS_FORBIDDEN                     = 403;
    const HTTP_STATUS_NOT_FOUND                     = 404;
    const HTTP_STATUS_METHOD_NOT_ALLOWED            = 405;
    const HTTP_STATUS_NOT_ACCEPTABLE                = 406;
    const HTTP_STATUS_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_STATUS_REQUEST_TIMEOUT               = 408;
    const HTTP_STATUS_CONFLICT                      = 409;
    const HTTP_STATUS_GONE                          = 410;
    const HTTP_STATUS_LENGTH_REQUIRED               = 411;
    const HTTP_STATUS_PRECONDITION_FAILED           = 412;
    const HTTP_STATUS_TOO_LARGE                     = 413;
    const HTTP_STATUS_TOO_LONG                      = 414;
    const HTTP_STATUS_UNSUPPORTED_MEDIA_TYPE        = 415;
    const HTTP_STATUS_RANGE_NOT_SATISFIABLE         = 416;
    const HTTP_STATUS_EXPECTATION_FAILED            = 417;
    const HTTP_STATUS_I_TEAPOT                      = 418;
    const HTTP_STATUS_UNPROCESSABLE_ENTITY          = 422;
    const HTTP_STATUS_LOCKED                        = 423;
    const HTTP_STATUS_FAILED_DEPENDENCY             = 424;
    const HTTP_STATUS_UPGRADE_REQUIRED              = 426;
    const HTTP_STATUS_PRECONDITION_REQUIRED         = 428;
    const HTTP_STATUS_TOO_MANY_REQUESTS             = 429;
    const HTTP_STATUS_HEADER_TOO_LARGE              = 431;
    const HTTP_STATUS_UNAVAILABLE_LEGAL_REASONS     = 451;
    const HTTP_STATUS_INTERNAL_SERVER_ERROR         = 500;
    const HTTP_STATUS_NOT_IMPLEMENTED               = 501;
    const HTTP_STATUS_BAD_GATEWAY                   = 502;
    const HTTP_STATUS_SERVICE_UNAVAILABLE           = 503;
    const HTTP_STATUS_GATEWAY_TIMEOUT               = 504;
    const HTTP_STATUS_VERSION_NOT_SUPPORTED         = 505;
    const HTTP_STATUS_VARIANT_VERSO_NEGOTIATES      = 506;
    const HTTP_STATUS_INSUFFICIENT_STORAGE          = 507;
    const HTTP_STATUS_LOOP_DETECTED                 = 508;
    const HTTP_STATUS_NOT_EXTENDED                  = 510;
    const HTTP_STATUS_NETWORK_AUTHENTICATION        = 511;
    /* Разное */
}
