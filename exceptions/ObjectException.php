<?php

use \gear\library\GException;

/**
 * Классы исключений базового класса объектов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 15.08.2015
 * @release 1.0.0
 */
class ObjectException extends GException {}
class ClassMethodNotFoundException extends ObjectException { public $defaultMessage = 'Method :className:::methodName not found'; }
class ClassStaticMethodNotFoundException extends ObjectException { public $defaultMessage = 'Static method :className:::methodName not found'; }
class ObjectInvalidPluginException extends ObjectException { public $defaultMessage = 'Plugin :pluginName is not callable and cannot be use as function'; }
class ObjectPluginNotRegisteredException extends ObjectException{ public $defaultMessage = 'Plugin :pluginName is not registered'; }
class ObjectInvalidBehaviorException extends ObjectException { public $defaultMessage = 'Behavior :behaviorName is invalid'; }
class ObjectBehaviorNotExistsException extends ObjectException { public $defaultMessage = 'Behavior ":behaviorName" is not exists'; }
class ObjectInvalidEventHandlerException extends ObjectException { public $defaultMessage = 'Invalid handler of event :eventName'; }
class ObjectPropertyIsReadOnlyException extends ObjectException { public $defaultMessage = 'Property :propertyName is read-only'; }
