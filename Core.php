<?php

namespace gear;

defined('GEAR') or define('GEAR', __DIR__);

/**
 * Ядро Gear Framework
 * 
 * @package Gear Framework
 * @final
 * @author Denisk Kukushkin
 * @copyright Denisk Kukushkin 2013
 * @version 0.0.1
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
    private static $_config = array         // Текущая конфигурация ядра
    (
        'preloads' => array
        (
            'library' => array
            (
                '\\gear\\library\\GException',
                '\\gear\\library\\GEvent',
                '\\gear\\CoreException',
                '\\gear\\interfaces\\IModule',
                '\\gear\\interfaces\\IComponent',
                '\\gear\\interfaces\\IPlugin',
                '\\gear\\library\\GObject',
                '\\gear\\library\\GModule',
                '\\gear\\library\\GComponent',
                '\\gear\\library\\GPlugin',
                '\\gear\\interfaces\\ILoader',
            ),
            'modules' => array
            (
            ),
            'components' => array
            (
                // Автозагрузчик классов
                'loader' => array('class' => '\\gear\\components\\gear\\loader\\GLoader'),
                // Обработчик ошибок
                'errorHandler' => array('class' => '\\gear\\components\\gear\\handlers\\GErrorsHandler'),
                // Обработчик неперехваченных исключений
                'exceptionHandler' => array('class' => '\\gear\\components\\gear\\handlers\\GExceptionsHandler'),
            ),
        ),
        'modules' => array
        (
        ),
        'components' => array
        (
        ),
    );
    private static $_modules = array();     // Подключенные модули
    private static $_components = array();  // Подключённые компоненты
    private static $_runMode = null;        // Режим запуска PRODUCTION или DEVELOPMENT
    private static $_env = null;            // Окружение http или консоль
    private static $_version = '0.0.1';     // Версия ядра
    /* Protected */
    /* Public */
    
    /**
     * Инициализация ядра
     * 
     * @access public
     * @static
     * @param string as path to configuration file|array of configuration $config
     * @param integer Core::MODE_DEVELOPMENT|Core::MODE_PRODUCTION $runMode
     * @throws \Exception
     * @return boolean
     */
    public static function init($config = null, $runMode = self::MODE_DEVELOPMENT)
    {
        self::$_runMode = $runMode;
        if ($config === null || (is_string($config) && is_dir($config)))
        {
            $config = ($config === null ? dirname($_SERVER['SCRIPT_FILENAME']) : $config) . '/config.' 
                    . (self::$_runMode === self::MODE_DEVELOPMENT ? 'debug' : 'production') 
                    . '.php';
        }
        if (is_string($config))
        {
            $pathFile = is_file($config) ? $config : self::resolvePath($config);
            if (!file_exists($pathFile))
                self::e('Конфигурационный файл ":initFile" не найден', array('initFile' => $config));
            $config = require($pathFile);
        }
        if (!is_array($config))
            self::e('Указанная конфигурация не является корректной');
        self::$_config = array_replace_recursive(self::$_config, $config);
        self::_preloads();
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
        /* Подгрузка библиотек */
        foreach(self::$_config['preloads']['library'] as $library)
        {
            $pathFile = self::resolvePath($library) . '.php';
            if (!file_exists($pathFile))
                self::e('Файл библиотеки ":pathFile" не найден', array('pathFile' => $pathFile));
            require($pathFile);
        }
        /* Подгрузка и установка модулей */
        foreach(self::$_config['preloads']['modules'] as $name => $module)
        {
            list($class, $config, $properties) = self::getRecords($module);
            $pathFile = self::resolvePath($class) . '.php';
            if (!file_exists($pathFile))
                self::e('Файл класса модуля ":moduleName" не найден', array('moduleName' => $name));
            require($pathFile);
            self::$_modules[$name] = $class::install($config, $properties);
        }
        /* Подгрузка и установка компонентов */
        foreach(self::$_config['preloads']['components'] as $name => $component)
        {
            list($class, $config, $properties) = self::getRecords($component);
            $pathFile = self::resolvePath($class) . '.php';
            if (!file_exists($pathFile))
                self::e('Файл класса компонента ":componentName" не найден', array('componentName' => $name));
            require($pathFile);
            self::$_components[$name] = $class::install($config, $properties);
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
    public static function app()
    {
        return self::m('app');
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
        if (!isset(self::$_modules[$name]))
        {
            if (!($module = self::isModuleRegistered($name)))
                self::e('Модуль ":moduleName" не зарегистрирован', array('moduleName' => $name));
            self::installModule($name, $module);
        }
        return self::$_modules[$name];
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
        if (isset(self::$_config['preloads']['modules'][$name]))
            return self::$_config['preloads']['modules'][$name];
        else
        if (isset(self::$_config['modules'][$name]))
            return self::$_config['modules'][$name];
        else
            return false;
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
        if ($module = self::isModuleRegistered($name))
        {
            if (!isset($module['override']) || !$module['override'])
                self::e('Модуль ":moduleName" не может быть перегружен', array('moduleName' => $name));
        }
        self::$_config['modules'][$name] = $module;
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
        return isset(self::$_modules[$name]) ? self::$_modules[$name] : null;
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
    public static function installModule($name, array $module, $owner = null)
    {
        if (isset(self::$_modules[$name]))
        {
            if (!self::$_modules[$name]->hasOverride())
                self::e('Модуль ":moduleName" не может быть перегружен', array('moduleName' => $name));
            self::uninstallModule($name);
        }
        return self::$_modules[$name] = self::_processElement($module, $owner);
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
        if (isset(self::$_modules[$name]))
        {
            self::$_modules[$name]->event('onUnstall');
            unset(self::$_modules[$name]);
            return true;
        }
        return false;
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
        if (!isset(self::$_components[$name]))
        {
            if (!($component = self::isComponentRegistered($name)))
                self::e('Компонент ":componentName" не зарегистрирован', array('componentName' => $name));
            self::installComponent($name, $component);
            return self::$_components[$name];
        }
        return $instance ? clone self::$_components[$name] : self::$_components[$name];
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
        if (isset(self::$_config['preloads']['components'][$name]))
            return self::$_config['preloads']['components'][$name];
        else
        if (isset(self::$_config['components'][$name]))
            return self::$_config['components'][$name];
        else
            return false;
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
        if ($c = self::isComponentRegistered($name))
        {
            if (!isset($c['override']) || !$c['override'])
                self::e('Компонент ":componentName" не может быть перегружен', array('componentName' => $name));
        }
        self::$_config['components'][$name] = $component;
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
        return isset(self::$_components[$name]) ? self::$_components[$name] : null;
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
        if (isset(self::$_components[$name]))
        {
            if (!self::$_components[$name]->hasOverride())
                self::e('Компонент ":componentName" не может быть перегружен', array('componentName' => $name));
            self::uninstallComponent($name);
        }
        return self::$_components[$name] = self::_processElement($component, $owner);
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
        if (isset(self::$_components[$name]))
        {
            self::$_components[$name]->event('onUnstall');
            unset(self::$_components[$name]);
            return true;
        }
        return false;
    }

    /**
     * Обработка элемента (получение и устновка модуля или компонента)
     * 
     * @access private
     * @static
     * @param array $element
     * @param null|object $owner
     * @return object
     */
    private static function _processElement(array $element, $owner = null)
    {
        list($class, $config, $properties) = self::getRecords($element);
        return $class::install($config, $properties, $owner);
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
            if (isset($config['__configFile']))
                $config = $config['__configFile'];
        }
        return array($class, $config, $properties);
    }
    
    /**
     * Получение физического пути для указанного пространства имён.
     * 
     * @access public
     * @static
     * @param string $path
     * @return null|string
     */
    public static function resolvePath($path)
    {
        $resolved = null;
        if (self::isComponentInstalled('loader'))
            $resolved = Core::c('loader')->resolvePath($path);
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
                $resolved = GEAR . '/..' 
                          . (Core::isModuleInstalled('app') ? Core::app()->getNamespace() . '/' : '/gear/')
                          . str_replace('\\', '/', $path);
            }
        }
        return $resolved;
    }
    
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
     * Возвращает режим запуска приложения
     * 
     * @access public
     * @static
     * @return boolean
     */
    public static function getMode() { return self::$_env ? self::$_env : (self::$_env = php_sapi_name() === 'cli' ? self::CLI : self::HTTP); }
    
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
     * Генерация исключения
     * 
     * @access public
     * @static
     * @param string $message
     * @param array $params
     * @throws \Exception|\gear\CoreException
     * @return void
     */
    public static function e($message, array $params = array())
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
