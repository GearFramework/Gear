<?php

namespace gear;

use gear\library\GEvent;

defined('GEAR') or define('GEAR', __DIR__);
defined('ROOT') or define('ROOT', dirname(__DIR__));

/**
 * Класс ядра фреймворка
 *
 * @final
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
final class Core
{
    /* Traits */
    /* Const */
    const CLI = 1;
    const HTTP = 2;
    const HTTPS = 3;
    const AJAX = 4;
    /**
     * @var int Режим запуска приложения - в разработке
     */
    const DEVELOPMENT = 1;
    /**
     * @var int Режим запуска приложения - в продакшене
     */
    const PRODUCTION = 2;
    /**
     * @var int доступ к объекту, у которого свойство $object->_access имеет данное значение, закрыт для всех
     */
    const ACCESS_PRIVATE = 0;
    /**
     * @var int защищённый доступ к объекту, у которого свойство $object->_access имеет данное значение,
     * требуется проверка прав доступа
     */
    const ACCESS_PROTECTED = 1;
    /**
     * @var int публичный доступ к объекту, у которого свойство $object->_access имеет данное значение
     */
    const ACCESS_PUBLIC = 2;
    /**
     * @var string класс log-сообщений alert
     */
    const ALERT = 'alert';
    /**
     * @var string класс log-сообщений critical
     */
    const CRITICAL = 'critical';
    /**
     * @var string класс log-сообщений debug
     */
    const DEBUG = 'debug';
    /**
     * @var string класс log-сообщений emergency
     */
    const EMERGENCY = 'emergency';
    /**
     * @var string класс log-сообщений error
     */
    const ERROR = 'error';
    /**
     * @var string класс log-сообщений exception
     */
    const EXCEPTION = 'exception';
    /**
     * @var string класс log-сообщений info
     */
    const INFO = 'info';
    /**
     * @var string класс log-сообщений notice
     */
    const NOTICE = 'notice';
    /**
     * @var string класс log-сообщений warning
     */
    const WARNING = 'warning';
    /* Private */
    /* Protected */
    /**
     * @var array $_config конфигурация ядра и системы
     */
    protected static $_config = [
        /* Элементы, которые будут загружены при инициализации ядра фреймворка */
        'bootstrap' => [
            /* Список загружаемых библиотек */
            'libraries' => [
                '\Psr\Http\Message\*',
                '\gear\interfaces\*',
                '\gear\traits\*',
                '\gear\library\GException',
                '\gear\exceptions\*',
                '\gear\library\GEvent' => 'GEvent',
                '\gear\library\GBehavior',
                '\gear\library\GObject',
                '\gear\library\GObject',
                '\gear\library\GService',
                '\gear\library\GModule',
                '\gear\library\GComponent',
                '\gear\library\GPlugin',
                '\gear\plugins\templater\GView',
            ],
            /* Список загружаемых модулей */
            'modules' => [],
            /* Список загружаемых компонентов */
            'components' => [
                /* Обработчик ошибок */
                'errorsHandler' => ['class' => '\gear\components\handlers\GErrorsHandlerComponent'],
                /* Обработчик исключений */
                'exceptionHandler' => ['class' => '\gear\components\handlers\GExceptionsHandlerComponent'],
                /* */
                'lang' => ['class' => '\gear\components\international\GInternationalComponent'],
                /* Автозагрузчик файлов с классами */
                'loader' => ['class' => '\gear\components\loader\GLoaderComponent'],
            ],
            'helpers' => [
                'ArrayHelper' => '\gear\helpers\HArray',
            ],
        ],
        /* Список глобальных зарегистрированных модулей системы */
        'modules' => [
            /* Модуль приложения должен быть описан всегда */
//            'app' => ['class' => '\gear\library\GApplication']
        ],
        /* Список глобальных зарегистрированных компонентов системы */
        'components' => [],
        /* Список глобальных свойств ядра */
        'properties' => [
            /* Режим запуска приложения */
            'mode' => self::DEVELOPMENT,
            /* Текущая локаль */
            'locale' => 'ru_RU',
            /* Кодировка */
            'charset' => 'utf-8',
            /* Файлы для записи логов ядра (должен быть прямой путь к файлу) */
            'syslog' => [
                self::ALERT => GEAR . '/logs/core/core.alert.log',
                self::CRITICAL =>  GEAR . '/logs/core/core.critical.log',
                self::DEBUG =>  GEAR . '/logs/core/core.debug.log',
                self::EMERGENCY => GEAR . '/logs/core/core.emergency.log',
                self::ERROR => GEAR . '/logs/core/core.error.log',
                self::EXCEPTION => GEAR . '/logs/core/core.exception.log',
                self::INFO => GEAR . '/logs/core/core.info.log',
                self::NOTICE => GEAR . '/logs/core/core.notice.log',
                self::WARNING => GEAR . '/logs/core/core.warning.log',
                0 => GEAR . '/logs/core/core.log',
            ],
            /* Разделять логи по файлам в зависимости от типа или всё писать в один общий лог */
            'splitLogs' => false,
            /* Название компонента автозагрузчика классов */
            'loaderName' => 'loader',
            'international' => 'lang',
        ],
    ];
    /**
     * @var string[] Строковые значения режимов запуска
     */
    protected static $_modes = [
        self::DEVELOPMENT => 'development',
        self::PRODUCTION => 'production',
    ];
    /**
     * @var \gear\interfaces\IService[] Массив установленных сервисов (модули, компоненты)
     */
    protected static $_services = [];
    /**
     * @var array Массив обработчиков событий
     */
    protected static $_events = [];
    /**
     * @var bool по-умолчанию false, принимает true когда заканчивается инициализация ядра
     */
    protected static $_initialized = false;
    /* Public */

    /**
     * Конструктор класса, закрыт
     *
     * @since 0.0.1
     * @version 0.0.1
     */
    private function __construct() {}

    /**
     * Клонирование объектов класса, закрыто
     *
     * @since 0.0.1
     * @version 0.0.1
     */
    private function __clone() {}

    /**
     * Сериализация закрыта
     *
     * @since 0.0.1
     * @version 0.0.1
     */
    private function __sleep() {}

    /**
     * Десериализация закрыта
     *
     * @since 0.0.1
     * @version 0.0.1
     */
    private function __wakeup() {}

    /**
     * В зависимости от указанных параметров метод может возвращать
     *  - Генерация исключения, если $name начинаяется с 'exception'
     *  - Вызов события если $name начинается с 'on' с последующей заглавной буквой
     *  - Зарегестрированный сервис (модуль, компонент)
     *  - Значение свойства ядра
     *
     * @param string $name
     * @param array $arguments
     * @return \Exception|mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if ('exception' === strtolower(substr($name, 0, 9))) {
            return self::e($name, ...$arguments);
        } else if (preg_match('/^on[A-Z]/', $name)) {
            return self::trigger($name, ...$arguments);
        } else if (self::isService($name)) {
            return self::service($name, ...$arguments);
        } else {
            return self::props($name, ...$arguments);
        }
    }

    /**
     * Начальная загрузка необходимых библиотек и сервисов для дальнейшей работы ядра и приложения
     *
     * @return void
     * @uses self::_bootstrapLibraries()
     * @uses self::_bootstrapModules()
     * @uses self::_bootstrapComponents()
     * @since 0.0.1
     * @version 0.0.1
     */
    private static function _bootstrap()
    {
        foreach(self::$_config['bootstrap'] as $sectionName => $section) {
            $method = '_bootstrap' . ucfirst($sectionName);
            if (method_exists(self::class, $method))
                self::$method($section);
        }
    }

    /**
     * Загрузка компонентов
     *
     * @param array $section
     * @return void
     * @used-by self::_bootstrap()
     * @since 0.0.1
     * @version 0.0.1
     */
    private static function _bootstrapComponents(array $section)
    {
        foreach($section as $name => $service) {
            self::installService($name, $service, 'component');
        }
    }

    /**
     * Загрузка хелперов
     *
     * @param array $section
     * @return void
     * @used-by self::_bootstrap()
     * @since 0.0.1
     * @version 0.0.1
     */
    private static function _bootstrapHelpers(array $section)
    {
        foreach($section as $helperAlias => $helperClass) {
            self::c('loader')->setAlias($helperClass, "\\$helperAlias");
        }
    }

    /**
     * Загрузка необходимых библиотек, интерфейсов, трейтов и пр.
     *
     * @param array $section
     * @return void
     * @used-by self::_bootstrap()
     * @since 0.0.1
     * @version 0.0.1
     */
    private static function _bootstrapLibraries(array $section)
    {
        foreach($section as $key => $library) {
            if (preg_match('/[*|?]/', basename($library))) {
                $library = self::resolvePath($library, true);
                foreach(glob($library) as $file) {
                    if (is_file($file) && is_readable($file)) {
                        require_once $file;
                    }
                }
            } else {
                if (!is_numeric($key)) {
                    $alias = $library;
                    $library = $key;
                } else {
                    $alias = null;
                }
                if (preg_match('/\.php$/i', $library)) {
                    $file = $library;
                    $class = null;
                } else {
                    $class = $library;
                    $file = $library . '.php';
                }
                $file = self::resolvePath($file, true);
                if (!file_exists($file)) {
                    throw self::exceptionCore('Bootstrap library <{lib}> not found', ['lib' => $file]);
                }
                require_once $file;
                if ($alias !== null && $class !== null) {
                    class_alias($class, $alias);
                }
            }
        }
    }

    /**
     * Загрузка модулей
     *
     * @param array $section
     * @return void
     * @used-by self::_bootstrap()
     * @since 0.0.1
     * @version 0.0.1
     */
    private static function _bootstrapModules(array $section)
    {
        foreach($section as $name => $service) {
            self::installService($name, $service, 'module');
        }
    }

    /**
     * возвращает текущий (выполняемый в данный момент) модуль приложения
     *
     * @return interfaces\IModule
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function app(): \gear\interfaces\IModule
    {
        return self::m('app');
    }

    /**
     * Возвращает конфигурацию объекта в виде массива из трёх элементов
     *
     * 1. Класс объекта
     * 2. Статические свойства класса (конфигурация класс)
     * 3. Свойства объекта
     *
     * @param array $config
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function configure(array $config): array
    {
        $class = null;
        $properties = $config;
        if (isset($properties['class'])) {
            $class = $properties['class'];
            if (is_array($class)) {
                if (isset($class['name'])) {
                    $config = $class;
                    $class = $config['name'];
                    unset($config['name']);
                }
            }
            unset($properties['class']);
        } else {
            $config = [];
        }
        return [$class, $config, $properties];
    }

    /**
     * Возвращает экземляр зарегистрированного компонента ядра
     *
     * @param string $name
     * @param interfaces\IObject $owner
     * @param bool $clone
     * @return interfaces\IComponent
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function c(string $name, \gear\interfaces\IObject $owner = null, bool $clone = false): \gear\interfaces\IComponent
    {
        $component = self::service($name, 'component', $owner);
        return $clone ? clone $component : $component;
    }

    /**
     * Возвращает экземпляр исключения
     *
     * @param string $exceptionName
     * @param mixed $message
     * @param mixed $context
     * @param mixed $code
     * @param null|\Exception $previous
     * @return \Exception
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function e(string $exceptionName, $message = '', $context = [], $code = 0, $previous = null): \Exception
    {
        $nameInt = substr($exceptionName, 9);
        $exceptionClass =  '\\' . $nameInt . 'Exception';
        $exception = null;
        if (is_array($message)) {
            $previous = $code ?: null;
            $code = !is_array($context) ?: 0;
            $context = $message;
            $message = 'Throw unknown exception';
        }
        if (self::isComponentInstalled(self::props('international'))) {
            $international = self::service(self::props('international'));
            $message = $international->tr($message, 'exceptions\\' . $nameInt);
        }
        foreach($context as $name => $value) {
            $message = str_replace('{' . $name . '}', $value, $message);
        }
        if (!class_exists($exceptionClass, false)) {
            $exception = new \Exception($message, $code, $previous);
        } else {
            $exception = new $exceptionClass($message, $code, $previous, $context);
        }
        return $exception;
    }

    /**
     * Возвращает конфигурацию ядра
     *
     * @param string $section
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getConfiguration(string $section = ''): array
    {
        if ($section !== '') {
            $c = isset(self::$_config[$section]) ? self::$_config[$section] : [];
        } else {
            $c = self::$_config;
        }
        return $c;
    }

    /**
     * Возаращает установленный сервис
     *
     * @param string $name
     * @param string|null $type
     * @return interfaces\IService
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getInstalledService(string $name, string $type = null): \gear\interfaces\IService
    {
        $service = null;
        if (!$type) {
            if (isset(self::$_services['components'][$name]))
                $service = self::$_services['components'][$name];
            else if (isset(self::$_services['modules'][$name]))
                $service = self::$_services['modules'][$name];
        } else {
            $type .= 's';
            if (isset(self::$_services[$type][$name]))
                $service = self::$_services[$type][$name];
        }
        return $service;
    }

    /**
     * Возвращает целочисленный режим запуска ядра, если $asString установлено в true, то возвращается
     * строковое значение режима
     *
     * @param bool $asString
     * @return int|string
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getMode(bool $asString = false)
    {
        return !$asString ? self::props('mode') : self::$_modes[self::props('mode')];
    }

    /**
     * Возаращает зарегистрированный сервис
     *
     * @param string $name
     * @param string|null $type
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getRegisteredService(string $name, string $type = null): array
    {
        $service = [];
        if (!$type) {
            if (isset(self::$_config['_bootstrap']['components'][$name]))
                $service = self::$_config['_bootstrap']['components'][$name];
            else if (isset(self::$_config['components'][$name]))
                $service = self::$_config['components'][$name];
            else if (isset(self::$_config['_bootstrap']['modules'][$name]))
                $service = self::$_config['_bootstrap']['modules'][$name];
            else if (isset(self::$_config['modules'][$name]))
                $service = self::$_config['modules'][$name];
        } else {
            $type .= 's';
            if (isset(self::$_config['_bootstrap'][$type][$name]))
                $service = self::$_config['_bootstrap'][$type][$name];
            else if (isset(self::$_config[$type][$name]))
                $service = self::$_config[$type][$name];
        }
        return $service;
    }

    /**
     * Возвращает тип сервиса
     *
     * @param interfaces\IService $service
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getTypeService(\gear\interfaces\IService $service): string
    {
        $type = '';
        if ($service instanceof \gear\interfaces\IModule)
            $type = 'module';
        else if ($service instanceof \gear\interfaces\IComponent)
            $type = 'component';
        else if ($service instanceof \gear\interfaces\IPlugin)
            $type = 'plugin';
        return $type;
    }

    /**
     * Инициализация ядра. В качестве параметра метод принимает массив конфигурационных параметров или путь к
     * конфигурационному файлу
     *
     * @param array|string|\Closure $config
     * @param int $mode
     * @throws \CoreException
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function init($config = [], int $mode = 0)
    {
        if ($config instanceof \Closure) {
            $config = $config();
        }
        if (is_string($config)) {
            $config = self::resolvePath($config, true);
            if (is_dir($config)) {
                $config .= '/config.' . (self::$_modes[$mode ?? self::props('mode')] ?? self::$_modes[self::PRODUCTION]) . '.php';
            } else {
                if (!preg_match('/\.php$/', $config))
                    $config .= '.php';
            }
            if (!file_exists($config) || !is_readable($config) || !is_file($config))
                throw self::exceptionCore('Invalid configuration file <{file}>', ['file' => $config]);
            $config = require $config;
        }
        if (!is_array($config))
            throw self::exceptionCore('Invalid configuration', ['config' => $config]);
        self::$_config = array_replace_recursive(self::$_config, $config);
        if ($mode && $mode !== (int)self::props('mode')) {
            self::props('mode', $mode);
        }
        self::_bootstrap();
        self::$_initialized = true;
    }

    /**
     * Установка компонента в ядро
     *
     * @param string $name
     * @param \gear\interfaces\IComponent|array $component
     * @param interfaces\IObject|null $owner
     * @return interfaces\IComponent
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function installComponent(string $name, $component, $owner = null): \gear\interfaces\IComponent
    {
        return self::installService($name, $component, 'component', $owner);
    }

    /**
     * Установка компонента в ядро
     *
     * @param string $name
     * @param \gear\interfaces\IModule|array $module
     * @param interfaces\IObject|null $owner
     * @return interfaces\IComponent
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function installModule(string $name, $module): \gear\interfaces\IComponent
    {
        return self::installService($name, $module, 'module');
    }

    /**
     * Установка сервиса в ядро
     *
     * @param string $name
     * @param \gear\interfaces\IService|array $service
     * @param string|null $type
     * @param interfaces\IObject|null $owner
     * @throws \CoreException
     * @return \gear\interfaces\IService
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function installService(string $name, $service, $type = '', $owner = null): \gear\interfaces\IService
    {
        if (is_array($service)) {
            list($class, $config, $properties) = self::configure($service);
            if (!self::isServiceInstalled(self::props('loaderName'), 'component')) {
                $file = self::resolvePath($class, true) . '.php';
                if (!file_exists($file) || !is_readable($file)) {
                    throw self::exceptionCore('Class <{className}> not found in file <{filePath}>', [
                        'className' => $class,
                        'filePath' => $file,
                    ]);
                }
                require_once($file);
            }
            $service = $class::install($config, $properties, $owner);
        }
        if (!($service instanceof \gear\interfaces\IService))
            throw self::exceptionCore('Installed service must be an instance of interface \gear\interfaces\IService');
        $type = (!$type ? self::getTypeService($service) : $type) . 's';
        if (isset(self::$_services[$type][$name])) {
            if (self::$_services[$type][$name]->props('__override__') !== true)
                throw self::exceptionCore('Service <{name}> already installed', ['name' => $name]);
        }
        self::$_services[$type][$name] = $service;
        return self::$_services[$type][$name];
    }

    /**
     * Возвращает true, если указанный компонент установлен
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function isComponentInstalled(string $name): bool
    {
        return self::isServiceInstalled($name, 'component');
    }

    /**
     * Возвращает true, если указанный компонент зарегистрирован в ядре
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function isComponentRegistered(string $name): bool
    {
        return self::isServiceRegistered($name, 'component');
    }

    /**
     * Возвращает true, если указанный компонент зарегистрирован в ядре
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function isModuleRegistered(string $name): bool
    {
        return self::isServiceRegistered($name, 'module');
    }

    /**
     * Возвращает true, если указанный компонент установлен
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function isModuleInstalled(string $name): bool
    {
        return self::isServiceInstalled($name, 'module');
    }

    /**
     * Возвращает true, если указанный именованый сервис зергистрирован или установлен в ядро, иначе возвращает false
     *
     * @param string $name
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function isService(string $name): bool
    {
        return self::isServiceInstalled($name) || self::isServiceRegistered($name);
    }

    /**
     * Возвращает true, если указанный сервис зарегистрирован
     *
     * @param string $name
     * @param string|null $type
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function isServiceRegistered(string $name, string $type = null): bool
    {
        if (!$type) {
            $is = isset(self::$_config['_bootstrap']['components'][$name]) || isset(self::$_config['components'][$name]) ||
                  isset(self::$_config['_bootstrap']['modules'][$name]) || isset(self::$_config['modules'][$name]) ?: false;

        } else {
            $type .= 's';
            $is = isset(self::$_config['_bootstrap'][$type][$name]) || isset(self::$_config[$type][$name]);
        }
        return $is;
    }

    /**
     * Возвращает true, если указанный сервис установлен
     *
     * @param string $name
     * @param string|null $type
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function isServiceInstalled(string $name, string $type = null): bool
    {
        if (!$type) {
            $is = isset(self::$_services['components'][$name]) || isset(self::$_services['modules'][$name]);
        } else {
            $type .= 's';
            $is = isset(self::$_services[$type][$name]);
        }
        return $is;
    }

    /**
     * @param string $name
     * @return interfaces\IModule
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function m(string $name): \gear\interfaces\IModule
    {
        return self::service($name, 'module');
    }

    /**
     * При опущенных параметрах возвращает массив свойств ядра, при указанному $name возвращает соответствующее
     * значение свойства, при указанном $name и $value устанавливает занчение для указанного свойства ядра
     *
     * @param null|string $name
     * @param null|mixed $value
     * @return null|mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function props(string $name = null, $value = null)
    {
        if ($name === null && $value === null) {
            return self::$_config['properties'];
        } else if ($name) {
            if ($value === null) {
                if (is_array($name)) {
                    $values = [];
                    foreach($name as $n) {
                        $values[$n] = self::$_config['properties'][$n] ?? null;
                    }
                    return $values;
                } else {
                    return self::$_config['properties'][$name] ?? null;
                }
            } else {
                self::$_config['properties'][$name] = $value;
            }
        }
    }

    /**
     * Регистрация компонента
     *
     * @param string $name
     * @param array $component
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function registerComponent(string $name, array $component)
    {
        self::registerService($name, $component, 'component');
    }

    /**
     * Регистрация модуля
     *
     * @param string $name
     * @param array $module
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function registerModule(string $name, array $module)
    {
        self::registerService($name, $module, 'module');
    }

    /**
     * Регистрация сервиса
     *
     * @param string|\Closure $name anonymous function must be return string
     * @param array|\Closure $service anonymous function must be return array
     * @param string $type
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function registerService($name, $service, string $type)
    {
        if ($name instanceof \Closure) {
            $name = $name($service, $type);
        }
        if (!is_string($name) || trim($name) === '')
            throw self::exceptionCore('Invalid name of registering service, name must be a string');
        if ($service instanceof \Closure) {
            $service = $service($name, $type);
        }
        if (!is_array($service) || empty($service))
            throw self::exceptionCore('Invalid configuration record of registering service, record must be a array');
        $type .= 's';
        if (isset(self::$_config[$type][$name])) {
            if (!isset(self::$_config[$type][$name]['__override__']) ||
                !self::$_config[$type][$name]['__override__']) {
                throw self::exceptionCore('Service <{name}> already registered', ['name' => $name]);
            }
        }
        self::$_config[$type][$name] = $service;
    }

    /**
     * Преобразует указанное значение пространства имён или относительный путь к файлу в
     * абсолютный путь
     *
     * @param string $path
     * @param bool $coreResolver
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function resolvePath(string $path, bool $coreResolver = false): string
    {
        if (!$coreResolver) {
            if (!($resolver = self::props('resolver')))
                $resolver = 'loader';
            if (self::isComponentInstalled($resolver)) {
                $path = self::c($resolver)->resolvePath($path);
                return $path;
            }
        }
        if (!$path) {
            return $path;
        }
        if (!preg_match('/^([a-zA-Z]\:|\/)/', $path)) {
            $resolve = str_replace('\\', '/', $path);
            if ($resolve[0] == '/') {
                $resolve = ROOT . $resolve;
            } else {
                if (self::isModuleInstalled('app')) {
                    $resolve = ROOT . '/' . str_replace('\\', '/', self::app()->namespace) . '/' . $resolve;
                } else {
                    $resolve = GEAR . '/' . $resolve;
                }
            }
        } else {
            $resolve = $path;
        }
        return $resolve;
    }

    /**
     * Получение сервиса по его названию, указанному при регистрации или установке
     *
     * @param string $name
     * @param string|null $type
     * @param interfaces\IObject|null $owner
     * @throws \CoreException
     * @return interfaces\IService
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function service(string $name, string $type = null, \gear\interfaces\IObject $owner = null): \gear\interfaces\IService
    {
        if (!self::isServiceInstalled($name, $type)) {
            if (!self::isServiceRegistered($name, $type)) {
                throw self::exceptionCore('Service <{name}> not registered', ['name' => $name]);
            }
            $service = self::getRegisteredService($name, $type);
            if ($service)
                $service = self::installService($name, $service, $type, $owner);
            else
                throw self::exceptionCore('Invalid configuration record for service <{service}>', ['service' => $name]);
        } else {
            $service = self::getInstalledService($name, $type);
        }
        return $service;
    }

    /**
     * Системное протоколирование
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @param bool $useCoreLog
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function syslog(string $level, string $message, array $context = [], bool $useCoreLog = false)
    {
        if (self::props('mode') === self::DEVELOPMENT) {
            if (!$useCoreLog && self::$_initialized && isset(self::$_services['components']['syslog'])) {
                self::$_services['components']['syslog']->log($level, $message, $context);
            } else {
                $logFiles = self::props('syslog');
                $logFile = (bool)self::props('splitLogs') === true && isset($logFiles[$level]) ? $logFiles[$level] : $logFiles[0];
                foreach($context as $name => $value) {
                    $message = str_replace('{' . $name . '}', $value, $message);
                }
                if (isset($context['__func__']) || isset($context['__line__'])) {
                    $info = [];
                    if (isset($context['__func__']))
                        $info[] = $context['__func__'] . '()';
                    if (isset($context['__line__']))
                        $info[] = $context['__line__'];
                    $message .= ' [' . implode(':', $info) . ']';
                }
                $log = date('d/m/Y H:i:s') . ' [' . strtoupper($level) . '] ' . $message . "\n";
                file_put_contents($logFile, $log, file_exists($logFile) ? FILE_APPEND : 0);
            }
        }
    }


    /**
     * Установка обработчика $handler на событие $name
     *
     * @param string $name
     * @param callable $handler
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function on(string $name, $handler)
    {
        if (!is_callable($handler))
            throw self::exceptionCore('Event handler must be callable');
        !isset(self::$_events[$name]) ? self::$_events[$name] = [$handler] : self::$_events[$name][] = $handler;
    }


    /**
     * Удаление всех или только указанного обработчика $handler события $name
     *
     * @param string $name
     * @param callable|bool $handler
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function off(string $name, $handler = false)
    {
        if (isset(self::$_events[$name])) {
            if (!$handler) {
                unset(self::$_events[$name]);
            } else {
                foreach (self::$_events[$name] as $i => $h) {
                    if ($h === $handler) {
                        unset(self::$_events[$name][$i]);
                    }
                }
            }
        }
    }

    /**
     * Вызов события
     *
     * @param string $name
     * @param object $event
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function trigger(string $name, $event): bool
    {
        $result = true;
        if (is_object($event->target) && method_exists($event->target, $name)) {
            $result = $event->target->$name($event);
        }
        if ($result && $event->bubble && isset(self::$_events[$name])) {
            foreach (self::$_events[$name] as $handler) {
                if (call_user_func($handler, $event) === false)
                    $result = false;
                if (!$event->bubble)
                    break;
            }
        }
        return $result;
    }

    /**
     * Деинсталляция компонента
     *
     * @param mixed $name
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstallComponent($name)
    {
        self::uninstallService($name, 'component');
    }

    /**
     * Деинсталляция модуля
     *
     * @param mixed $name
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstallModule($name)
    {
        self::uninstallService($name, 'module');
    }

    /**
     * Деинсталляция сервиса
     *
     * @param mixed $name
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstallService($name, string $type = '')
    {
        if ($name instanceof \Closure) {
            $name = $name($type);
        }
        if (!is_string($name))
            self::exceptionCore('Invalis name of service; must be a string');
        if (!self::isServiceRegistered($name, $type))
            self::exceptionCore('Error uninstalling <{name}> service; service not found', ['name' => $name]);
        if (!$type) {
            $service = self::getInstalledService($name);
            $type = self::getTypeService($service) . 's';
        } else {
            $type .= 's';
            $service = self::$_services[$type][$name];
        }
        unset(self::$_services[$type][$name]);
        $service->uninstall();
    }

    /**
     * Удаление регистрации компонента
     *
     * @param mixed $name
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function unregisterComponent($name)
    {
        self::unregisterService($name, 'component');
    }

    /**
     * Удаление регистрации модуля
     *
     * @param mixed $name
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function unregisterModule($name)
    {
        self::unregisterService($name, 'module');
    }

    /**
     * Удаление регистрации сервиса
     *
     * @param mixed $name
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function unregisterService($name, string $type)
    {
        if ($name instanceof \Closure) {
            $name = $name($type);
        }
        if (!is_string($name))
            self::exceptionCore('Invalis name of service; must be a string');
        if (!self::isServiceRegistered($name, $type))
            self::exceptionCore('Error unregistering <{name}> service; service not found', ['name' => $name]);
        $type .= 's';
        if (isset(self::$_config['_bootstrap'][$type][$name])) {
            unset(self::$_config['_bootstrap'][$type][$name]);
        } else if (isset(self::$_config[$type][$name])) {
            unset(self::$_config[$type][$name]);
        }
        self::trigger('onUnregisteredService', new GEvent(self::class, ['serviceName' => $name]));
    }
}
