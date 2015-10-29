<?php

//Issues
//TODO::Менять токен пользовательской сессии при каждом запросе
//TODO::Для форм добавлять скрытое поле с рандомным названием и значением, которое меняется при ошибках запроса из формы

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
 * @php 5.4.x or higher
 */
final class Core
{
    /* Const */

    /* Версия ядра */
    const VERSION = '1.0.0';

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

    const MODE_DEVELOPMENT = 0;
    const MODE_PRODUCTION = 1;

    const ACCESS_PRIVATE = 0;
    const ACCESS_PROTECTED = 1;
    const ACCESS_PUBLIC = 2;
    /* Private */
    /* Текущая конфигурация ядра */
    private static $_config =
    [
        /* Библиотеки, модули, компоненты подключаемые на этапе инициализации */
        'preloads' =>
        [
            /* Библиотеки подключаемые на этапе инициализации */
            'library' =>
            [
                '\gear\library\GException',
                '\gear\exceptions\*',
                '\gear\library\GEvent',
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
            ],
            /* Модули подключаемые на этапе инициализации */
            'modules' => [],
            /* Компоненты подключаемые на этапе инициализации */
            'components' =>
            [
                /* Системное логгирование */
                'syslog' =>
                [
                    'class' => ['name' => '\gear\components\gear\syslog\GSyslog'],
                    'name' => 'syslog',
                ],
                // Автозагрузчик классов
                'loader' =>
                [
                    'class' => ['name' => '\gear\components\gear\loader\GLoader'],
                    'name' => 'loader',
                    /* Set helpers, remove dependencies on the class name */
                    'aliases' =>
                    [
                        'Arrays' => ['class' => '\gear\helpers\GArray'],
                        'Calendar' => ['class' => '\gear\helpers\GCalendar'],
                        'Html' => ['class' => '\gear\helpers\GHtml'],
                    ],
                ],
                // Обработчик ошибок
                'errorHandler' =>
                [
                    'class' => '\gear\components\gear\handlers\GErrorsHandler',
                    'name' => 'errorHandler',
                ],
                // Обработчик неперехваченных исключений
                'exceptionHandler' =>
                [
                    'class' => '\gear\components\gear\handlers\GExceptionsHandler',
                    'name' => 'exceptionHandler',
                ],
            ],
        ],
        /* Модули ядра */
        'modules' => [],
        /* Компоненты ядра */
        'components' => [],
        /* Хелперы */
        'helpers' =>
        [
            'calendar' => ['class' => '\gear\helpers\GCalendar'],
        ],
        /* Параметры работы ядра, приложения и т.п. */
        'params' =>
        [
            'baseDir' => GEAR, 
            'locale' => 'ru_RU',
            'encoding' => 'utf-8',
            'services' => ['class' => '\gear\library\container\GServicesContainer'],
//            'configurator' => ['class' => '\gear\components\gear\configurator\GConfigurator'],
            'helperManager' => ['class' => '\gear\components\gear\helper\GHelperManager'],
            'defaultApplication' => ['class' => '\gear\library\GApplication'],
        ],
    ];
    /* Обработчики событий */
    private static $_events =
    [
        'onCoreReady' => [],
        'onBeforeApplicationRun' => [],
        'onAfterApplicationRun' => [],
        'onRequest' => [],
        'onBeforeProcessExec' => [],
        'onAfterProcessExec' => [],
        'onBeforeApiRun' => [],
        'onAfterApiRun' => [],
    ];
    /* Режим запуска ядра */
    private static $_coreMode = null;
    /* Окружение: http или консоль */
    private static $_runMode = null;
    /* Protected */
    /* Public */
    
    public static function __callStatic($name, $args)
    {
        if (preg_match('/^exception[A-Z]{1}/', $name))
        {
            if (!isset($args[0]) || is_array($args[0]))
                array_unshift($args, null);
            array_unshift($args, $name);
            $result = call_user_func_array([__CLASS__, 'e'], $args);
        }
        else
        if (self::isModuleRegistered($name))
            $result = self::m($name);
        else
        if (self::isComponentRegistered($name))
            $result = self::c($name, count($args) ? $args[0] : false);
        else
        if (self::isHelperRegistered($name))
        {
            array_unshift($args, $name);
            $result = call_user_func_array([__CLASS__, 'h'], $args);
        }
        else
        {
            array_unshift($args, $name);
            return call_user_func_array([__CLASS__, 'params'], $args);
        }
        return $result;
    }

    /**
     * Инициализация ядра
     *
     * @access public
     * @static
     * @param string|array|\Closure $config path to configuration file or array of configuration or anonymous function must return array
     * @param integer $coreMode Core::MODE_DEVELOPMENT|Core::MODE_PRODUCTION
     * @return boolean
     */
    public static function init($config = null, $coreMode = self::MODE_DEVELOPMENT)
    {
        Core::syslog('CORE -> Initialize...');
        $modes = [self::MODE_DEVELOPMENT => 'debug', self::MODE_PRODUCTION => 'production'];
        self::$_coreMode = $coreMode;
        if ($config instanceof \Closure)
            $config = $config($coreMode);
        else
        {
            if (!$config)
                $config = dirname($_SERVER['SCRIPT_FILENAME']) . '/config.' . $modes[self::$_coreMode] . '.php';
            if (is_string($config))
            {
                $fileConfig = self::resolvePath($config, true);
                clearstatcache();
                if (is_dir($fileConfig))
                    $fileConfig .= '/config.' . $modes[self::$_coreMode] . '.php';
                $config = is_file($fileConfig) ? require($fileConfig) : null;
            }
        }
        if (!is_array($config) || (is_array($config) && !$config))
            $config = ['modules' => ['app' => self::params('defaultApplication')]];
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
        Core::syslog('CORE -> Initialize end');
        return true;
    }

    /**
     * Подгрузка необходимых библиотек, модулей и компонентов на этапе
     * конфигурации ядра
     *
     * @access private
     * @static
     * @return bool
     */
    private static function _preloads()
    {
        Core::syslog('CORE -> Prepare preloads');
        foreach(self::$_config['preloads']['library'] as $class)
        {
            Core::syslog('CORE -> Preload library ' . $class);
            if (substr($class, -1) === '*')
            {
                $pathMask = self::resolvePath($class, true);
                $path = dirname($pathMask);
                $mask = basename($pathMask);
                self::_loadPath($path, $mask);
            }
            else
            {
                $pathFile = self::resolvePath($class, true) . '.php';
                self::_loadFile($pathFile);

            }
        }
        unset(self::$_config['preloads']['library']);
        foreach(self::$_config['preloads'] as $sectionName => $section)
        {
            Core::syslog('CORE -> Preload section ' . $sectionName);
            self::_preloadSection($sectionName, $section);
        }
        return true;
    }

    /**
     * Загрузка всех библиотек по указанному пути и маске файлов
     *
     * @access private
     * @param string $path
     * @param string $mask
     * @return void
     */
    private static function _loadPath($path, $mask)
    {
        $regexpMask = '#^' . str_replace('*', '.*', $mask) . '\.php$#i';
        foreach(scandir($path) as $file)
        {
            if ($file === '.' || $file === '..' || (is_file($file) && !preg_math($regexpMask, $file)))
                continue;
            $file = $path . '/' . $file;
            if (is_file($file))
                self::_loadFile($file);
            else
            if (is_dir($file))
                self::_loadPath($file, $mask);
        }
    }

    /**
     * Загрузка указнного файла-библиотеки
     *
     * @access private
     * @param string $file
     * @throws \Exception
     * @return void
     */
    private static function _loadFile($file)
    {
        if (!file_exists($file) || !is_readable($file))
            throw self::exceptionFileNotFound(['filename' => $file]);
        require $file;
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
            Core::syslog('CORE -> Preload class ' . $class . ' in library ' . $pathFile);
            self::_loadFile($pathFile);
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
            throw self::exceptionCore('Services container not defined');
        else
        if (!is_object($services))
        {
            list($class, $config, $properties) = self::getRecords($services);
            $file = self::resolvePath($class, true) . '.php';
            self::_loadFile($file);
            if (method_exists($class, 'init'))
                $class::init($config);
            $services = new $class($properties);
            self::params('services', $services);
        }
        return $services;
    }

    /**
     * Вызов компонента реализцющего системное протоколирование
     * Производит протоколирование оперций в случае, если такой компонент
     * установлен и константа DEBUG установлена в TRUE
     * 
     * @access public
     * @static
     * @return void
     */
    public static function syslog($log)
    {
        echo $log . "\n";
//        if (self::isComponentInstalled('syslog'))
//            call_user_func_array(self::c('syslog'), func_get_args());
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
     * Возвращает false если указанный сервис не зарегистрирован
     *
     * @access public
     * @static
     * @param string $name
     * @return bool|object
     */
    public static function isServiceRegistered($name)
    {
        if (!($result = self::isModuleRegistered($name)))
            $result = self::isComponentRegistered($name);
        return $result;
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
            throw self::exceptionCore('Module :moduleName not registered', ['moduleName' => $name]);
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
            throw self::exceptionCore('Component :componentName not registered', ['componentName' => $name]);
        return self::services()->getRegisteredService(__CLASS__ . '.components.' . $name, $instance);
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

    /**
     * Возвращает true, если указанный хелпер зарегистрирован, иначе false
     *
     * @access public
     * @static
     * @param string $name
     * @return boolean
     */
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
    public static function trigger($name, $event = null)
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
                if (($result instanceof \gear\library\GEvent && $result->stopPropagation === true) || !$result)
                    break;
            }
        }
        return $result;
    }
    
    /**
     * Установка события
     * 
     * @access public
     * @static
     * @param string $name
     * @param mixed $handler callable value
     * @return mixed
     */
    public static function on($name, $handler) { return self::attachEvent($name, $handler); }
    
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
            throw self::exceptionCore('Invalid handler of event :eventName', ['eventName' => $eventName]);
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
        $config = [];
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
            // Абсолютный путь
            if (preg_match('/^[a-zA-Z]{1}\:/', $path) || $path[0] === '/')
                $resolved = $path;
            // Относительный путь или пространство имён
            else
            {
                $resolved = GEAR . '/..';
                if ($path[0] !== '\\')
                    $resolved .= (is_object(self::params('services')) && Core::isModuleInstalled('app') ? Core::app()->getNamespace() . '/' : '\gear') . '/' . $path;
                else
                    $resolved .= $path;
                $resolved = str_replace('\\', '/', $resolved);
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
    public static function getVersion() { return self::VERSION; }

    /**
     * Создание указанного исключения
     *
     * @access public
     * @param string $exceptionName
     * @param string $message
     * @param array $params
     * @param int $code
     * @param null|object $previous
     * @return \Exception
     */
    public function e($exceptionName, $message, array $params = array(), $code = 0, $previous = null)
    {
        $exceptionName = '\\' . preg_replace('/^exception/', '', $exceptionName) . 'Exception';
        if (class_exists($exceptionName, false))
            $exception = new $exceptionName($message, $code, $previous, $params);
        else
        {
            foreach($params as $name => $value)
                $message = str_replace(':' . $name, $value, $message);
            $exception = new \Exception($message, $code, $previous);
        }
        return $exception;
    }

    public static function dump($value, $renderer = null)
    {
        if ($renderer && is_callable($renderer))
            $renderer('views\dump', ['value' => $value]);
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
