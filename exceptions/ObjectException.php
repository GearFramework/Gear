<?php

use \gear\library\GException;

/**
 * Классы исключений базового класса объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 01.08.2013
 * @release 1.0.0
 */
class ObjectException extends GException {}

class ObjectMethodNotFoundException extends GException
{
    public $defaultMessage = 'Method :className:::methodName not found';
}

class ObjectStaticMethodNotFoundException extends GException
{
    public $defaultMessage = 'Static method :className:::methodName not found';
}

class ObjectInvalidPluginException extends GException
{
    public $defaultMessage = 'Plugin :pluginName is not callable and cannot be use as function';
}

class ObjectPluginNotRegisteredException extends GException
{
    public $defaultMessage = 'Plugin :pluginName is not registered';
}

class ObjectInvalidBehaviorException extends GException
{
    public $defaultMessage = 'Behavior :behaviorName is invalid';
}

class ObjectBehaviorNotExistsException extends GException
{
    public $defaultMessage = 'Behavior ":behaviorName" is not exists';
}

class ObjectInvalidEventHandlerException extends GException
{
    public $defaultMessage = 'Invalid handler of event :eventName';
}
