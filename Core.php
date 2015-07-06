<?php

namespace gear;

defined('GEAR') or define('GEAR', __DIR__);
defined('DEBUG') or define('DEBUG', false);

/**
 * Ядро Gear Framework
 *
 * 
 * @package Gear Framework
 * @final
 * @author Denis Kukushkin
 * @copyright Denis Kukushkin 2013
 * @version 1.0.0
 * @since 01.08.2013
 * @license MIT
 * @php 5.3.x
 */
final class Core
{
    /* Const */
    const EMERGENCY = 'EMERGENCY';
    const ALERT = 'ALERT';
    const CRITICAL = 'CRITICAL';
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const NOTICE = 'NOTICE';
    const INFO = 'INFO';
    const DEBUG = 'DEBUG';
    const HTTP = 1;
    const CLI = 2;
    const MODE_DEVELOPMENT = 1;
    const MODE_PRODUCTION = 2;
    const ACCESS_PRIVATE = 0;
    const ACCESS_PROTECTED = 1;
    const ACCESS_PUBLIC = 2;
    /* Private */
    /* Текущая конфигурация ядра */
    private static $_config = array
    (
        /* Библиотеки, модули, компоненты подключаемые на этапе инициализации */
        'preloads' => array
        (
            /* Библиотеки подключаемые на этапе инициализации */
            'library' => array
            (
                '\gear\library\GException',
                '\gear\library\GEvent',
                '\gear\CoreException',
                '\gear\interfaces\IService',
                '\gear\interfaces\IModule',
                '\gear\interfaces\IComponent',
                '\gear\interfaces\IPlugin',
                '\gear\library\GObject',
                '\gear\library\GService',
                '\gear\library\GModule',
                '\gear\library\GComponent',
                '\gear\library\GPlugin',
                '\gear\interfaces\ILoader',
            ),
            /* Модули подключаемые на этапе инициализации */
            'modules' => array(),
            /* Компоненты подключаемые на этапе инициализации */
            'components' => array
            (
                /* Системное логгирование */
                'syslog' => array
                (
                    'class' => array('name' => '\gear\components\gear\syslog\GSyslog'),
                    'name' => 'syslog',
                ),
                // Автозагрузчик классов
                'loader' => array
                (
                    'class' => array('name' => '\gear\components\gear\loader\GLoader'),
                    'name' => 'loader',
                    /* Set helpers, remove dependencies on the class name */
                    'aliases' =>  array
                    (
                        'Arrays' => array('class' => 'gear\helpers\GArray'),
                        'Calendar' => array('class' => 'gear\helpers\GCalendar'),
                        'Html' => array('class' => 'gear\helpers\GHtml'),
                    ),
                ),
                // Обработчик ошибок
                'errorHandler' => array
                (
                    'class' => '\gear\components\gear\handlers\GErrorsHandler',
                    'name' => 'errorHandler',
                ),
                // Обработчик неперехваченных исключений
                'exceptionHandler' => array
                (
                    'class' => '\gear\components\gear\handlers\GExceptionsHandler',
                    'name' => 'exceptionHandler',
                ),
            ),
        ),
        /* Модули ядра */
        'modules' => array(),
        /* Компоненты ядра */
        'components' => array
        (
            'helper' => array
            (
                'class' => '\gear\components\gear\helper\GHelperManager',
                'name' => 'helper',
            ),
        ),
        'helpers' => array
        (
            'calendar' => array('class' => '\gear\helpers\GCalendar'),
        ),
        /* Параметры работы ядра, приложения и т.п. */
        'params' => array
        (
            'baseDir' => GEAR, 
            'locale' => 'ru_RU',
            'encoding' => 'utf-8',
            'services' => array('class' => '\gear\library\container\GServicesContainer'),
            'configurator' => array('class' => '\gear\components\gear\configurator\GConfigurator'),
            'defaultApplication' => array('class' => '\gear\library\GApplication'),
            'helperManager' => 'helper',
        ),
    );
    /* Обработчики событий */
    private static $_events = array();
    /* Режим запуска ядра */
    private static $_coreMode = null;
    /* Окружение: http или консоль */
    private static $_runMode = null;
    /* Версия ядра */
    private static $_version = '1.0.0';
    /* Protected */
    /* Public */
    
    public static function __callStatic($name, $args)
    {
        if (self::isModuleRegistered($name))
            return self::m($name);
        if (self::isComponentRegistered($name))
            return self::c($name, count($args) ? $args[0] : false);
        if (self::isHelperRegistered($name))
        {
            array_unshift($args, $name);
            return call_user_func_array(array(__CLASS__, 'h'), $args);
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
            $file = self::resolvePath($class, true) . '.php';
            require $file;
            if (method_exists($class, 'init'))
                $class::init($config);
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
     * @param string|array|\Closure $config path to configuration file or array of configuration or anonymous function must return array
     * @param integer $coreMode Core::MODE_DEVELOPMENT|Core::MODE_PRODUCTION
     * @throws \Exception
     * @return boolean
     */
    public static function init($config = null, $coreMode = self::MODE_DEVELOPMENT)
    {
        $modes = array(self::MODE_DEVELOPMENT => 'debug', self::MODE_PRODUCTION => 'production');
        self::$_coreMode = $coreMode;
        if ($config instanceof \Closure)
            $config = $config($coreMode = self::MODE_DEVELOPMENT);
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
            $config = array('modules' => array('app' => self::params('defaultApplication')));
        self::$_config = array_replace_recursive(self::$_config, $config);
        self::_preloads();
        foreach(self::$_config as $sectionName => $section)
        {
            if ($sectionName != 'params' && $sectionName != 'preloads')
            {
                $section = self::_prepareConfig($section);
                foreach($section as $serviceName => $service)
                {
                    $serviceLocation = __CLASS__ . '.' . $sectionName . '.' . $serviceName;
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
        foreach(self::$_config['preloads']['library'] as $class)
        {
            $pathFile = self::resolvePath($class, true) . '.php';
            if (!file_exists($pathFile))
                self::e('File ":pathFile" not found', array('pathFile' => $pathFile));
            require($pathFile);
        }
        unset(self::$_config['preloads']['library']);
        foreach(self::$_config['preloads'] as $sectionName => $section)
        {
            self::_preloadSection ($sectionName, $section);
        }
        return true;
    }
    
    /**
     * Подгрузка модулей и компонентов на этапе конфигурации ядра
     * 
     * @access private
     * @static
     * @param string $sectionName
     * @param array $section
     * @return void
     */
    private static function _preloadSection($sectionName, array $section)
    {
        foreach($section as $serviceName => $service)
        {
            list($class, $config, $properties) = self::getRecords($service);
            $pathFile = self::resolvePath($class, true) . '.php';
            if (!file_exists($pathFile))
                self::e('File ":preloadName" not found', array('preloadName' => $pathFile));
            require($pathFile);
            $instance = $class::install($config, $properties);
            self::services()->installService(__CLASS__ . '.' . $sectionName . '.' . $serviceName, $instance);
        }
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
        if (!self::isModuleRegistered($name))
            self::e('Module :moduleName not registered', array('moduleName' => $name));
        return self::services()->getRegisteredService(__CLASS__ . '.modules.' . $name);
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
        return self::services()->isRegisteredService(__CLASS__ . '.modules.' . $name);
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
        self::services()->registerService(__CLASS__ . '.modules.' . $name, $module);
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
        return self::services()->isInstalledService(__CLASS__ . '.modules.' . $name);
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
        return self::services()->installService(__CLASS__ . '.modules.' . $name, $module);
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
        self::services()->uninstallService(__CLASS__ . '.modules.' . $name);
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
        if (!self::isComponentRegistered($name))
            self::e('Component :componentName not registered', array('componentName' => $name));
        return self::params('services')->getRegisteredService(__CLASS__ . '.components.' . $name, $instance);
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
        return self::services()->isRegisteredService(__CLASS__ . '.components.' . $name);
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
        self::services()->registerService(__CLASS__ . '.components.' . $name, $component);
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
        return self::services()->isInstalledService(__CLASS__ . '.components.' . $name);
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
        return self::services()->installService(__CLASS__ . '.components.' . $name, $component);
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
        self::services()->uninstallService(__CLASS__ . '.components.' . $name);
        return true;
    }

    /**
     * Возвращает доступ к указанному хелперу
     *
     * @access public
     * @static
     * @param string $name
     * @return object
     */
    public static function h($name)
    {
        $helperManager = self::params('helperManager');
        if (($helper = Core::isComponentInstalled($helperManager)) === false)
        {
            $helper = Core::c($helperManager);
            $helper->registerHelpers(self::$_config['helpers']);
        }
        else
            $helper = Core::c($helperManager);
        return $helper->runHelper($name);
    }

    public static function isHelperRegistered($name)
    {
        $helperManager = self::params('helperManager');
        if (($helper = Core::isComponentInstalled($helperManager)) === false)
        {
            $helper = Core::c($helperManager);
            $helper->registerHelpers(self::$_config['helpers']);
        }
        return $helper->isHelperRegistered($name);
    }
    
    /**
     * генерация события
     * 
     * @access public
     * @static
     * @param string $name
     * @param object $event
     * @return mixed
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
     * @return mixed
     */
    public static function on($name, $event) { return call_user_func_array(array(__CLASS__, 'event'), func_get_args()); }
    
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
            self::e('Invalid handler of event ":eventName"', array('eventName' => $eventName));
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
        $properties = self::_prepareConfig($properties);
        $class = null;
        $config = array();
        if (isset($properties['class']))
        {
            $class = $properties['class'];
            unset($properties['class']);
            if (is_array($class))
            {
                $config = $class;
                $class = $config['name'];
                unset($config['name']);
                $config = self::_prepareConfig($config);
            }
        }
        return array($class, $config, $properties);
    }

    /**
     * Подготовка конфигурационных параметров
     *
     * @access private
     * @param array $config
     * @return array
     */
    private static function _prepareConfig(array $config)
    {
        if (isset($config['#import']))
        {
            $imports = !is_array($config['#import']) ? array($config['#import']) : $config['#import'];
            unset($config['#import']);
            foreach($imports as $param)
            {
                $import = self::params($param);
                $config = array_replace_recursive($config, self::_prepareConfig($import));
            }
        }
        if (isset($config['#include']))
        {
            $includes = !is_array($config['#include']) ? array($config['#include']) : $config['#include'];
            unset($config['#include']);
            foreach($includes as $file)
            {
                $file = self::resolvePath($file, true);
                $config = array_replace_recursive($config, self::_prepareConfig(require($file)));
            }
        }
        return $config;
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
            {
                $resolved = GEAR . '/../';
                if ($path[0] !== '\\')
                    $resolved .= (is_object(self::params('services')) && Core::isModuleInstalled('app') ? Core::app()->getNamespace() . '/' : 'gear/');
                $resolved .= str_replace('\\', '/', $path);
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
    public static function getVersion() { return self::$_version; }
    
    /**
     * Генерация исключения
     * 
     * @access public
     * @static
     * @param string $message
     * @param integer $code
     * @param \Exception $previous
     * @param array $params
     * @param string $class
     * @throws \Exception|\gear\CoreException
     * @return \Exception
     */
    public static function e($message, array $params = array(), $code = 0, $previous = null, $class = __CLASS__)
    {
        $count = count($args = func_get_args());
        for($i = 1; $i < $count; ++ $i)
        {
            if (is_numeric($args[$i]))
                $code = $args[$i];
            else
            if ($args[$i] instanceof \Exception)
                $previous = $args[$i];
            else
            if (is_array($args[$i]))
                $params = $args[$i];
            else
            if (is_string($args[$i]))
                $class = $args[$i];
        }
        $classException = $class . 'Exception';
        if ($class !== __CLASS__)
        {
            if (!class_exists($classException, false))
            {
                $classException = static::_e($class);
                if (!class_exists($classException, false))
                    $classException = static::_e(get_parent_class($class));
            }
        }
        if (!class_exists($classException, false))
        {
            foreach($params as $name => $value)
                $message = str_replace(':' . $name, $value, $message);
            throw new \Exception($message, $code, $previous);
        }
        throw new $classException($message, $code, $previous, $params);
    }

    private static function _e($class)
    {
        $path = str_replace('\\', '/', $class);
        return str_replace('/', '\\', dirname($path) . '/' . substr(basename($path), 1) . 'Exception');
    }

    public static function dump($value, $renderer = null)
    {
        if ($renderer && is_callable($renderer))
            $renderer('views\dump', array('value' => $value));
        else
        {
            echo '<pre>';
            if (is_array($value) || is_object($value))
                echo print_r($value, 1);
            else
                var_dump($value);
            echo '</pre>';
        }
    }
}
