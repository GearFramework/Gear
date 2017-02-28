<?php

class HttpException extends \gear\library\GException {}
class HttpBadRequestException extends HttpException
{
    protected $_defaultMessage = 'Bad request';
}
class HttpNotFoundException extends HttpException
{
    protected $_defaultMessage = 'Not found';
}