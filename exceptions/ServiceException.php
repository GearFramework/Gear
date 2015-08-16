<?php

use \gear\library\GException;

/**
 * Классы исключений сервисов (модули, компоненты, плагины, хелперы)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 15.08.2015
 * @release 1.0.0
 */
class ServiceException extends GException {}
class ServiceNotRegisteredException extends GException { public $defualtMessage = 'Service :serviceName not registered'; }
class ServiceComponentNotRegisteredException extends ServiceException { public $defaultMessage = 'Component :componentName is not registered'; }
class HelperException extends ServiceException {}
class HelperNotFoundException extends HelperException { public $defaultMessage = 'Helper :helperName not found'; }
class ApplicationException extends ServiceException {}
class CacheInvalidServerException extends ServiceException { public $defaultMessage = 'Invalid server'; }
class LoaderException extends ServiceException {}
class LoaderClassFileNotFound extends LoaderException { public $defaultMessage = 'File :filename of class :className not found'; }
