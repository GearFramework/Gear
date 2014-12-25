<?php

namespace gear;

defined('GEAR') or define('GEAR', __DIR__);
defined('DEBUG') or define('DEBUG', false);

/**
 * Ядро Gear Framework
 * 
 * @package Gear Framework
 * @final
 * @author Denisk Kukushkin
 * @copyright Denisk Kukushkin 2013
 * @version 1.0.0
 * @since 01.08.2013
 * @license MIT
 */
final class Core
{
    /* Const */
    const HTTP = 1;
    const CLI = 2;
    const MODE_DEVELOPMENT = 1;
    const MODE_PRODUCTION = 2;
    const ACCESS_PRIVATE = 0;
    const ACCESS_PROTECTED = 1;
    const ACCESS_PUBLIC = 2;
    /* Private */
    private static $_config =           // Текущая конфигурация ядра
    [         
        'preloads' => 
        [
            'library' => 
            [
                '\gear\library\GException',
                '\gear\library\GEvent',
                '\gear\CoreException',
                '\gear\interfaces\IModule',
                '\gear\interfaces\IComponent',
                '\gear\interfaces\IPlugin',
                '\gear\library\GObject',
                '\gear\traits\TNamedService',
                '\gear\library\GModule',
                '\gear\library\GComponent',
                '\gear\library\GPlugin',
                '\gear\interfaces\ILoader',
                '\gear\GServicesContainer',
            ],
            'modules' => [],
            'components' =>
            [
                'syslog' => 
                [
                    'class' => '\gear\components\gear\syslog\GSyslog',
                    'nameService' => 'syslog',
                ],
                // Автозагрузчик классов
                'loader' => 
                [
                    'class' => '\gear\components\gear\loader\GLoader',
                    'nameService' => 'loader',
                    'aliases' => 
                    [
                        'Arrays' => ['class' => 'gear\helpers\GArray'],
                        'Calendar' => ['class' => 'gear\helpers\GCalendar'],
                        'Html' => ['class' => 'gear\helpers\GHtml'],
                    ],
                ],
                // Обработчик ошибок
                'errorHandler' => 
                [
                    'class' => '\gear\components\gear\handlers\GErrorsHandler',
                    'nameService' => 'errorHandler',
                ],
                // Обработчик неперехваченных исключений
                'exceptionHandler' => 
                [
                    'class' => '\gear\components\gear\handlers\GExceptionsHandler',
                    'nameService' => 'exceptionHandler',
                ],
            ],
        ],
        'modules' => [],
        'components' => [],
        'params' => 
        [
            'baseDir' => GEAR, 
            'locale' => 'ru_RU',
            'encoding' => 'utf-8',
            'services' => ['class' => '\gear\GServicesContainer'],
        ],
    ];
    private static $_modules = [];      // Подключенные модули
    private static $_components = [];   // Подключённые компоненты
    private static $_events = [];       // Обработчики событий
    private static $_coreMode = null;   // Режим запуска PRODUCTION или DEVELOPMENT
    private static $_runMode = null;    // Окружение: http или консоль
    private static $_version = '1.0.0'; // Версия ядра
    /* Protected */
    /* Public */
    
    public static function __callStatic($name, $args)
    {
        if (self::isModuleRegistered($name))
            return self::m($name);
        if (self::isComponentRegistered($name))
            return self::c($name, count($args) ? $args[0] : false);
        if (isset(self::$_config['helpers'][$name]))
        {
            array_unshift($args, $name);
            return call_user_func_array([__CLASS__, 'h'], $args);
        }
        array_unshift($args, $name);
        return call_user_func_array(array(__CLASS__, 'params'), $args);
            
    }
    
    /**
     * Возвращает инстанс менеджера сервисов
     * 
     * @access public
     * @static
     * @return object
     */
    public static function services()
    {
        $services = self::params('services');
        if (!$services)
            self::e('Services container not defined');
        else
        if (!is_object($services))
        {
            list($class, $config, $properties) = self::getRecords($services);
            $services = new $class($properties);
            self::params('services', $services);
        }
        return $services;
    }
    
    /**
     * Инициализация ядра
     * 
     * @access public
     * @static
     * @param string as path to configuration file|array of configuration $config
     * @param integer Core::MODE_DEVELOPMENT|Core::MODE_PRODUCTION $coreMode
     * @throws \Exception
     * @return boolean
     */
    public static function init($config = ['modules' => ['app' => ['class' => '\gear\library\GApplication']]], 
                                $coreMode = self::MODE_DEVELOPMENT)
    {
        $modes = [self::MODE_DEVELOPMENT => 'debug', self::MODE_PRODUCTION => 'production'];
        self::$_coreMode = $coreMode;
        if ($config === null)
            $config = dirname($_SERVER['SCRIPT_FILENAME']) . '/config.' . $modes[self::$_coreMode] . '.php';
        if (is_string($config))
        {
            $fileConfig = self::resolvePath($config, true);
            if (is_dir($fileConfig))
                $fileConfig .= '/config.' . $modes[self::$_coreMode] . '.php';
            $config = is_file($fileConfig) ? require($fileConfig) : null;
        }
        if (!is_array($config))
            self::e('Invalid configuration');
        self::$_config = array_replace_recursive(self::$_config, $config);
        self::_preloads();
        foreach(self::$_config as $sectionName => $section)
        {
            if ($sectionName != 'params' && $sectionName != 'preloads')
            {
                foreach($section as $serviceName => $service)
                {
                    $serviceLocation = self::class . '.' . $sectionName . '.' . $serviceName;
                    self::services()->registerService($serviceLocation, $service);
                }
            }
        }
        return true;
    }
    
    /**
     * Подгрузка необходимых библиотек, модулей и компонентов на этапе
     * конфигурации ядра
     * 
     * @access private
     * @static
     * @return boolean
     */
    private static function _preloads()
    {
        foreach(self::$_config['preloads'] as $section => $preloads)
        {
            foreach($preloads as $preloadName => $preload)
            {
                switch($section)
                {
                    case 'library' :
                    {
                        $pathFile = self::resolvePath($preload, true) . '.php';
                        if (!file_exists($pathFile))
                            self::e('File ":pathFile" not found', array('pathFile' => $pathFile));
                        require($pathFile);
                        break;
                    }
                    case 'modules' :
                    case 'components' :
                    {
                        list($class, $config, $properties) = self::getRecords($preload);
                        $pathFile = self::resolvePath($class, true) . '.php';
                        if (!file_exists($pathFile))
                            self::e('File ":preloadName" not found', array('preloadName' => $preloadName));
                        require($pathFile);
                        $instance = $class::install($config, $properties);
                        self::services()->installService(self::class . '.' . $section . '.' . $preloadName, $instance);
                    }
                }
            }
        }
        return true;
    }

    /**
     * Получение доступа к модулю приложения
     * 
     * @access public
     * @static
     * @return \gear\library\GApplication
     */
    public static function app() { return self::m('app'); }
    
    /**
     * Вызов компонента реализцющего системное протоколирование
     * Производит протоколирование оперций в случае, если такой компонент
     * установлен и константа DEBUG установлена в TRUE
     * 
     * @access public
     * @static
     * @return void
     */
    public static function syslog()
    {
        if (self::isComponentInstalled('syslog'))
            call_user_func_array(self::c('syslog'), func_get_args());
    }
    
    /**
     * Получение или установка значения для глобального аргумента
     * 
     * @access public
     * @static
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public static function params($name, $value = null)
    {
        if ($value === null)
            return isset(self::$_config['params'][$name]) ? self::$_config['params'][$name] : null;
        self::$_config['params'][$name] = $value;
        return $value;
    }
    
    /**
     * Получение модуля
     * 
     * @access public
     * @static
     * @param string $name
     * @return \gear\library\GModule
     */
    public static function m($name)
    {
        return self::services()->getRegisteredService(self::class . '.modules.' . $name);
    }
    
    /**
     * Возвращает запись модуля, если таковой зарегистрирован иначе false
     * 
     * @access public
     * @static
     * @param string $name
     * @return false|array
     */
    public static function isModuleRegistered($name)
    {
        return self::services()->isRegisteredService(self::class . '.modules.' . $name);
    }
    
    /**
     * Регистрация модуля
     * 
     * @access public
     * @static
     * @param string $name
     * @param array $module
     * @throws \Exception
     * @return true
     */
    public static function registerModule($name, array $module)
    {
        self::services()->registerService(self::class . '.modules.' . $name, $module);
        return true;
    }
    
    /**
     * Возвращает установленный модуль или false иначе
     * 
     * @access public
     * @static
     * @param string $name
     * @return null|object
     */
    public static function isModuleInstalled($name)
    {
        return self::services()->isInstalledService(self::class . '.modules.' . $name);
    }
    
    /**
     * Установка модуля
     * 
     * @access public
     * @static
     * @param string $name
     * @param array $module
     * @param null|object $owner
     * @return \gear\library\GModule
     */
    public static function installModule($name, array $module)
    {
        return self::services()->installService(self::class . '.modules.' . $name, $module);
    }
    
    /**
     * Удаление установленного модуля
     * 
     * @access public
     * @static
     * @param string $name
     * @return boolean
     */
    public static function uninstallModule($name)
    {
        self::services()->uninstallService(self::class . '.modules.' . $name);
        return true;
    }
    
    /**
     * Получение компонента
     * 
     * @access public
     * @static
     * @param string $name
     * @return \gear\library\GComponent
     */
    public static function c($name, $instance = false)
    {
        return self::params('services')->getRegisteredService(self::class . '.components.' . $name, $instance);
    }
    
    /**
     * Возвращает запись компонента, если таковой зарегистрирован иначе false
     * 
     * @access public
     * @static
     * @param string $name
     * @return false|array
     */
    public static function isComponentRegistered($name)
    {
        return self::params('services')->isRegisteredService(self::class . '.components.' . $name);
    }
    
    /**
     * Регистрация компонента
     * 
     * @access public
     * @static
     * @param string $name
     * @param array $module
     * @throws \Exception
     * @return true
     */
    public static function registerComponent($name, array $component)
    {
        self::services()->registerService(self::class . '.components.' . $name, $component);
        return true;
    }
    
    /**
     * Возвращает установленный компонент или false иначе
     * 
     * @access public
     * @static
     * @param string $name
     * @return object|false
     */
    public static function isComponentInstalled($name)
    {
        return self::services()->isInstalledService(self::class . '.components.' . $name);
    }
    
    /**
     * Установка компонента
     * 
     * @access public
     * @static
     * @param string $name
     * @param array $module
     * @param null|object $owner
     * @return \gear\library\GModule
     */
    public static function installComponent($name, array $component, $owner = null)
    {
        if ($owner)
            $properties['owner'] = $owner;
        return self::services()->installService(self::class . '.components.' . $name, $component);
    }
    
    /**
     * Удаление установленного компонента
     * 
     * @access public
     * @static
     * @param string $name
     * @return boolean
     */
    public static function uninstallComponent($name)
    {
        self::params('services')->uninstallService(self::class . '.components.' . $name);
        return true;
    }
    
    /**
     * генерация события
     * 
     * @access public
     * @static
     * @param string $name
     * @param object $event
     * @return void
     */
    public static function event($name, $event)
    {
        $result = false;
        if (isset(self::$_events[$name]))
        {
            $args = func_get_args();
            array_shift($args);
            if (!$event)
                $args[0] = new \gear\library\GEvent(null);
            foreach(self::$_events[$name] as $handler)
            {
                $result = call_user_func_array($handler, $args);
                if (($result instanceof \gear\library\GEvent && 
                    $result->stopPropagation === true) || !$result)
                    break;
            }
        }
        return $result;
    }
    
    /**
     * генерация события
     * 
     * @access public
     * @static
     * @param string $name
     * @param object $event
     * @return void
     */
    public static function on($name, $event)
    {
        return self::event($name, $event);
    }
    
    /**
     * Добавление обработчика события
     * 
     * @access public
     * @static
     * @param string $eventName
     * @param mixed $handler callable value
     * @return boolean
     */
    public static function attachEvent($eventName, $handler)
    {
        if (!is_callable($handler))
            self::e('Invalid handler of event ":eventName"', ['eventName' => $eventName]);
        self::$_events[$eventName][] = $handler;
        return true;
    }
    
    /**
     * Получение значений класса, конфигурации и свойств из
     * специально сформированной структуры
     * 
     * @access public
     * @static
     * @param array $properties
     * @return array as Array(className, configuration, properties)
     */
    public static function getRecords(array $properties)
    {
        $class = $properties['class'];
        unset($properties['class']);
        $config = array();
        if (is_array($class))
        {
            $config = $class;
            $class = $config['name'];
            unset($config['name']);
            self::_includeRecords($config);
            self::_importRecords($config);
        }
        self::_includeRecords($properties);
        self::_importRecords($properties);
        return array($class, $config, $properties);
    }
    
    private static function _importRecords(array &$array)
    {
        if (isset($array['#import']))
        {
            $import = $array['#import'];
            unset($array['#import']);
            if (is_array($import))
            {
                foreach($import as $importName)
                    $array[$importName] = self::params($importName);
            }
            else
                $array[$import] = self::params($import);
        }
    }
    
    private static function _includeRecords(array &$array)
    {
        if (isset($array['#include']))
        {
            $include = require(self::resolvePath($array['#include']));
            unset($array['#include']);
            if (is_array($include))
                $array = array_replace_recursive($array, $include);
        }
    }
    
    /**
     * Получение физического пути для указанного пространства имён.
     * 
     * @access public
     * @static
     * @param string $path
     * @return null|string
     */
    public static function resolvePath($path, $internalResolver = false)
    {
        $resolved = null;
        if (!$internalResolver)
        {
            if (self::isComponentInstalled('loader') && method_exists(Core::c('loader'), 'resolvePath'))
                $resolved = Core::c('loader')->resolvePath($path);
        }
        else
        {
            /* Абсолютный путь */
            if (preg_match('/^[a-zA-Z]{1}\:/', $path) || $path[0] === '/')
                $resolved = $path;
            /* Относительный путь или пространство имён */
            else
            if ($path[0] === '\\')
                $resolved = GEAR . '/..' . str_replace('\\', '/', $path);
            else
            {
                $resolved = GEAR . '/..' . (Core::isModuleInstalled('app') ? Core::app()->getNamespace() . '/' : '/gear/')
                          . str_replace('\\', '/', $path);
            }
        }
        return $resolved;
    }
    
    /**
     * Возвращает режим запуска приложения
     * 
     * @access public
     * @static
     * @return boolean
     */
    public static function getMode() 
    { 
        return self::$_runMode ? self::$_runMode : (self::$_runMode = php_sapi_name() === 'cli' ? self::CLI : self::HTTP); 
    }
    
    /**
     * Возвращает true если приложение запущено из браузера, иначе false
     * 
     * @access public
     * @static
     * @return boolean
     */
    public static function isHttp() { return self::getMode() === self::HTTP; }
    
    /**
     * Возвращает true если приложение запущено из консоли, иначе false
     * 
     * @access public
     * @static
     * @return boolean
     */
    public static function isCli() { return self::getMode() === self::CLI; }
    
    /**
     * Возвращает версию ядра фреймворка
     * 
     * @access public
     * @static
     * @return string
     */
    public static function getVersion()
    {
        return self::$_version;
    }
    
    /**
     * Генерация исключения
     * 
     * @access public
     * @static
     * @param string $message
     * @param array $params
     * @throws \Exception|\gear\CoreException
     * @return void
     */
    public static function e($message, array $params = [])
    {
        if (!class_exists('\gear\CoreException', false))
        {
            foreach($params as $name => $value)
                $message = str_replace(':' . $name, $value, $message);
            throw new \Exception($message);
        }
        else
            throw new \gear\CoreException($message, $params);
    }
    
    public static function dump($value)
    {
        echo '<pre>';
        if (is_array($value) || is_object($value))
            echo print_r($value, 1);
        else
            var_dump($value);
        echo '</pre>';
    }
}
