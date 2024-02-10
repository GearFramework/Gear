<?php

namespace Gear;

use Exception;
use Gear\Components\Autoloader\Autoloader;
use Gear\Enums\RunMode;
use Gear\Enums\ServiceType;
use Gear\Exceptions\CoreException;
use Gear\Interfaces\ApplicationInterface;
use Gear\Interfaces\AutoloaderInterface;
use Gear\Interfaces\Services\ComponentInterface;
use Gear\Interfaces\Services\ModuleInterface;
use Gear\Interfaces\Services\ServiceInterface;
use Gear\Library\Application;
use Gear\Library\GearException;
use Gear\Library\Services\Container;
use Throwable;

defined('PRIVATE_ROOT') or define('PRIVATE_ROOT', __DIR__ . '/..');
define('GEAR_ROOT', __DIR__);

/**
 * Ядро фреймворка
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
final class Core
{
    /* Traits */
    /* Const */
    /* Private */
    private static array $config = [
        /* Дополнительные элементы, которые будут загружены при инициализации ядра фреймворка */
        'bootstrap'  => [
            /* Список пользовательских загружаемых библиотек */
            'libraries'  => [],
            /* Список загружаемых модулей */
            ServiceType::Module->value    => [],
            /* Список загружаемых компонентов */
            ServiceType::Component->value => [
                /* Автозагрузчик файлов с классами */
                'loader' => [
                    'class' => Autoloader::class
                ],
//                'lang' => ['class' => '\Gear\Components\International\GInternationalComponent'],
            ],
            ServiceType::Helper->value    => [
//                'Arrays' => ['class' => '\Gear\Helpers\ArrayHelper'],
//                'Html' => ['class' => '\Gear\Helpers\HtmlHelper'],
//                'Calendar' => ['class' => '\Gear\Helpers\CalendarHelper'],
            ],
        ],
        /* Список глобальных зарегистрированных модулей системы */
        ServiceType::Module->value    => [
            /* Модуль приложения должен быть описан всегда */
            'app' => ['class' => Application::class],
        ],
        /* Список глобальных зарегистрированных компонентов системы */
        ServiceType::Component->value => [],
        /* Список пользовательских хэлперов */
        ServiceType::Helper->value    => [],
        /* Список моделей */
        'models'     => [],
        /* Список глобальных свойств ядра */
        'properties' => [
            /* Режим запуска приложения */
            'runMode'           => RunMode::Development,
            /* Текущая локаль */
            'locale'            => 'ru_RU',
            /* Кодировка */
            'charset'           => 'utf-8',
            /* Временная зона */
            'timezone'          => 'Europe/Moscow',
            /* Файлы для записи логов ядра (должен быть прямой путь к файлу) */
            'syslog'            => [],
            /* Разделять логи по файлам в зависимости от типа или всё писать в один общий лог */
            'splitLogs'         => false,
            /* Название компонента автозагрузчика классов */
            'pathResolverName'  => 'loader',
            'classLoaderName'   => 'loader',
            'routerName'        => 'router',
            'international'     => 'lang',
        ],
    ];
    private static Container $container;
    /* Protected */
    /* Public */

    /**
     * Конструктор класса, закрыт
     */
    private function __construct() {}

    /**
     * Клонирование объектов класса, закрыто
     *
     * @return void
     */
    private function __clone(): void {}

    /**
     * Сериализация закрыта
     */
    public function __sleep() {}

    /**
     * Десериализация закрыта
     *
     * @return void
     */
    public function __wakeup(): void {}

    /**
     * В зависимости от указанных параметров метод может возвращать
     *  - Генерация исключения, если $name заканчивается на 'Exception'
     *  - Вызов события если $name начинается с 'on' с последующей заглавной буквой
     *  - Зарегистрированный сервис (модуль, компонент)
     *  - Значение свойства ядра
     *
     * @param   string  $name
     * @param   array   $arguments
     * @return  mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        if (str_ends_with($name, 'Exception')) {
            /**
             * Вызвано исключение, например:
             *
             * Core::Gear\Exceptions\CoreException('Message');
             * Core::Gear\Exceptions\CoreException('Invalid filename {filename}', ['filename' => '/home/file.txt']);
             */
            if (is_array($arguments[0])) {
                array_unshift($arguments, '');
            }
            return self::exception($name, ...$arguments);
        }
        if (self::isRegistered($name)) {
            /**
             * Вызван зарегистрированный сервис (модуль или компонент),
             * например, Core::loader()->resolvePath('dir/sub_dir');
             */
            return self::get($name);
        }
        /**
         * Возвращает установленный параметр ядра или null, если таковой не найден, например,
         * Core::locale();, если передать параметр, то будет установлено значение, для указанного
         * параметра, например, Core::locale('en_EN');
         */
        return self::props($name, ...$arguments);
    }

    /**
     * Возвращает объект исключения, указанного класса
     *
     * @param   string          $exceptionClass
     * @param   string          $message
     * @param   array           $context
     * @param   int             $code
     * @param   Throwable|null  $previous
     * @return  Exception
     */
    public static function exception(
        string $exceptionClass,
        string $message = '',
        array $context = [],
        int $code = 0,
        ?Throwable $previous = null
    ): Exception {
        /** @var Exception|GearException $exception */
        $exception = new $exceptionClass($message, $code, $previous, $context);
        return $exception;
    }

    public static function dump(...$args): void
    {
        echo '<pre>';
        foreach ($args as $arg) {
            if (is_array($arg) || is_object($arg)) {
                echo var_export($arg, true), '; ';
                continue;
            }
            echo $arg, '; ';
        }
        echo '</pre>';
    }

    /**
     * Инициализация запуска приложения
     *
     * @param   array|string  $config
     * @param   RunMode       $runMode
     * @return  void
     */
    public static function init(array|string $config, RunMode $runMode = RunMode::Development): void
    {
        self::$container = new Container();
        $config = self::prepareConfig($config, $runMode);
        if (is_array($config) === false) {
            throw self::{CoreException::class}('Invalid configuration', ['config' => $config]);
        }
        self::$config = array_replace_recursive(self::$config, $config);
        self::props('runMode', $runMode);
        self::bootstrap();
    }

    /**
     * Обработка и получение конфига запускаемого приложения
     *
     * @param   array|string  $config
     * @param   RunMode       $runMode
     * @return  array|null
     */
    private static function prepareConfig(array|string $config, RunMode $runMode = RunMode::Development): ?array
    {
        if (is_string($config)) {
            $config = self::resolvePath($config, true);
            if (file_exists($config) === false) {
                throw self::{CoreException::class}('Configuration <{file}> not found', ['file' => $config]);
            }
            if (is_dir($config)) {
                $modeName = ucfirst($runMode->value);
                $config .= "/{$modeName}.php";
            }
            if (str_ends_with($config, '.php') === false) {
                $config .= '.php';
            }
            if (file_exists($config) === false
                || is_readable($config) === false
                || is_file($config) === false
            ) {
                throw self::{CoreException::class}('Invalid configuration file <{file}>', ['file' => $config]);
            }
            $config = require $config;
        }
        return is_array($config) ? $config : null;
    }

    /**
     * Начальная загрузка необходимых библиотек и сервисов для
     * дальнейшей работы ядра и приложения
     *
     * @return void
     */
    private static function bootstrap(): void
    {
        self::bootstrapSection(self::$config['bootstrap'][ServiceType::Module->value]);
        self::bootstrapSection(self::$config['bootstrap'][ServiceType::Component->value]);
        self::bootstrapHelpers(self::$config['bootstrap'][ServiceType::Helper->value]);
    }

    /**
     * Загрузка и установка переданного массива сервисов
     *
     * @param   array $section
     * @return  void
     */
    private static function bootstrapSection(array $section): void
    {
        foreach ($section as $name => $service) {
            self::install($name, $service);
        }
    }

    /**
     * Загрузка и установка массива хелперов
     *
     * @param   array $helpersSection
     * @return  void
     */
    private static function bootstrapHelpers(array $helpersSection): void
    {
        /** @var AutoloaderInterface $loader */
        $loader = self::get(self::props('classLoaderName'));
        foreach ($helpersSection as $helperAlias => $helperClass) {
            $loader->setAlias($helperClass, "\\$helperAlias");
        }
    }

    /**
     * Возвращает инстанс приложения
     *
     * @return ApplicationInterface
     */
    public static function app(): ApplicationInterface
    {
        /** @var ApplicationInterface $app */
        $app = self::get('app');
        return $app;
    }

    /**
     * Преобразует указанное значение пространства имён или относительный путь к файлу в
     * абсолютный путь
     *
     * @param   string  $path
     * @param   bool    $useNativeResolve
     * @return  string
     */
    public static function resolvePath(string $path, bool $useNativeResolve = false): string
    {
        $path = trim($path);
        if ($path === '') {
            return $path;
        }
        if ($useNativeResolve === false && self::isRegistered(self::props('pathResolverName'))) {
            /** @var AutoloaderInterface $resolver */
            $resolver = self::get(self::props('pathResolverName'));
            return $resolver->resolvePath($path);
        }
        if (preg_match('/^([a-zA-Z]:|\/)/', $path)) {
            return $path;
        }
        $resolve = str_replace('\\', '/', $path);
        if ($resolve[0] === '/') {
            return PRIVATE_ROOT . $resolve;
        }
        if (self::isInstalled('app')) {
            $namespace = str_replace('\\', '/', self::app()->namespace);
            return PRIVATE_ROOT . "/{$namespace}/{$resolve}";
        }
        return $resolve = GEAR_ROOT . "/{$resolve}";
    }

    /**
     * Установка/получение установленных свойств ядра
     *
     * @param   array|string|null   $name
     * @param   mixed|null          $value
     * @return  mixed
     */
    public static function props(array|string|null $name = null, mixed $value = null): mixed
    {
        if ($name === null && $value === null) {
            return self::$config['properties'];
        }
        if (empty($name)) {
            return null;
        }
        if ($value !== null) {
            self::$config['properties'][$name] = $value;
            return true;
        }
        if (is_array($name)) {
            $values = [];
            foreach ($name as $corePropertyName) {
                $values[$corePropertyName] = self::$config['properties'][$corePropertyName] ?? null;
            }
            return $values;
        }
        return self::$config['properties'][$name] ?? null;
    }

    /**
     * Возвращает true если сервис под указанным названием зарегистрирован в конфиге
     *
     * @param   string $serviceName
     * @return  bool
     */
    public static function isRegistered(string $serviceName): bool
    {
        return isset(self::$config['bootstrap'][ServiceType::Component->value][$serviceName])
            || isset(self::$config[ServiceType::Component->value][$serviceName])
            || isset(self::$config['bootstrap'][ServiceType::Module->value][$serviceName])
            || isset(self::$config[ServiceType::Module->value][$serviceName]);
    }

    /**
     * Возвращает зарегистрированный сервис
     *
     * @param   string $serviceName
     * @return  array|null
     */
    public static function getRegistered(string $serviceName): ?array
    {
        if (isset(self::$config['bootstrap']['components'][$serviceName])) {
            return self::$config['bootstrap']['components'][$serviceName];
        }
        if (isset(self::$config['components'][$serviceName])) {
            return self::$config['components'][$serviceName];
        }
        if (isset(self::$config['bootstrap']['modules'][$serviceName])) {
            return self::$config['bootstrap']['modules'][$serviceName];
        }
        if (isset(self::$config['modules'][$serviceName])) {
            return self::$config['modules'][$serviceName];
        }
        return null;
    }

    /**
     * Возвращает true указанный сервис установлен
     *
     * @param   string $serviceName
     * @return  bool
     */
    public static function isInstalled(string $serviceName): bool
    {
        return isset(self::$container[$serviceName]) === true;
    }

    /**
     * Возвращает инстанс зарегистрированного сервиса
     *
     * @param   string $serviceName
     * @return  ServiceInterface|ModuleInterface|ComponentInterface
     */
    public static function get(string $serviceName): ServiceInterface|ModuleInterface|ComponentInterface
    {
        if (self::$container->isset($serviceName)) {
            return self::$container->get($serviceName);
        }
        $serviceConfig = self::getRegistered($serviceName);
        if ($serviceConfig === null) {
            self::{CoreException::class}('Service <{serviceName}> not registered', ['serviceName' => $serviceName]);
        }
        return self::install($serviceName, $serviceConfig);
    }

    /**
     * Возвращает название пространства имён класса.
     *
     * @param   string $class
     * @return  string
     */
    public static function getNamespace(string $class): string
    {
        return substr($class, 0, strrpos($class, '\\'));
    }

    /**
     * Регистрация сервиса
     *
     * @param   string      $serviceName
     * @param   array       $config
     * @param   ServiceType $serviceType
     * @return  bool
     */
    public static function register(
        string $serviceName,
        array $config,
        ServiceType $serviceType = ServiceType::Component
    ): bool {
        self::$config[$serviceType->value][$serviceName] = $config;
        return true;
    }

    /**
     * Получение зарегистрированного сервиса и установка его в ядре
     *
     * @param   string  $serviceName
     * @param   array   $serviceConfig
     * @return  ServiceInterface|ModuleInterface|ComponentInterface
     */
    public static function install(
        string $serviceName,
        array $serviceConfig
    ): ServiceInterface|ModuleInterface|ComponentInterface {
        list($class, $config, $properties) = self::configure($serviceConfig);
        if (empty($class)) {
            self::{CoreException::class}(
                'Service <{serviceName}> not found class',
                ['serviceName' => $serviceName],
            );
        }
        $serviceInstance = $class::install($config, $properties);
        self::set($serviceName, $serviceInstance);
        return $serviceInstance;
    }

    /**
     * Установка сервиса
     *
     * @param   string            $serviceName
     * @param   ServiceInterface  $serviceInstance
     * @return  void
     */
    public static function set(string $serviceName, ServiceInterface $serviceInstance): void
    {
        self::$container->set($serviceName, $serviceInstance);
    }

    /**
     * Возвращает конфигурацию объекта в виде массива из трёх элементов
     *
     * 0 => Класс объекта или null
     * 1 => Статические свойства класса (конфигурация класса Class::$config)
     * 2 => Свойства объекта
     *
     * @param   string|array $config
     * @return  array
     */
    public static function configure(string|array $config): array
    {
        $class = null;
        if (is_string($config)) {
            $config = match ($config[0]) {
                '#' => include_once self::resolvePath(substr($config, 1)),
                '@' => self::getRegistered(substr($config, 1)),
                '%' => self::props(substr($config, 1)),
                default => [],
            };
        }
        if (is_array($config) === false) {
            $config = [];
        }
        $properties = $config;
        $config = [];
        $class = $properties['class'] ?? null;
        if (is_array($class)) {
            $config = $class;
            $class = null;
            if (isset($config['name'])) {
                $class = $config['name'];
                unset($config['name']);
            }
        }
        unset($properties['class']);
        return [$class, $config, $properties];
    }
}
