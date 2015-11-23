<?php

use gear\library\GException;
use gear\traits\TView;

/**
 * Исключения запросов, процессов, api
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 12.11.2015
 */
class HttpException extends GException
{
    use TView;
    private $_httpErrorCodes =
    [
        404 => 'Not found',
    ];
    protected $_viewPath = '/gear/views';
    public $defaultMessage = 'Http error';
}
