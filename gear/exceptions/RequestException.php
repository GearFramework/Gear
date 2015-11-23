<?php

use \gear\library\GException;

/**
 * Исключения запросов, процессов, api
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 16.08.2015
 */
class RequestException extends GException {}

class ProcessException extends RequestException {}
class ProcessApiNotExistsException extends ProcessException { public $defaultMessage = 'Api :apiName is not exists in process :processName'; }
class ApiInvalidRequestParameterException extends ProcessException { public $defaultMessage = 'Api :apiName required parameter :argName'; }

class InvalidRequestHandlerException extends RequestException { public $defaultMessage = 'Invalid server'; }
class InvalidRequestMethodException extends RequestException { public $defaultMessage = 'Ivalid request method :requestMethod'; }
class InvalidHeaderPluginException extends RequestException { public $defaultMessage = 'Invalid header plugin'; }
class InvalidSenderPluginException extends RequestException { public $defaultMessage = 'Invalid sender plugin'; }
class InvalidHttpRequestMethodException extends RequestException { public $defaultMessage = 'Invalid http request method :requestMethod'; }
