<?php

namespace gear\library;

use gear\Core;
use gear\interfaces\IObject;
use gear\interfaces\IService;

/**
 * Базовый класс сервисов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GService extends GObject implements IService
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    /* Public */

    /**
     * Установка сервиса
     *
     * @param array|string|\Closure $config
     * @param array|string|\Closure $properties
     * @param \gear\interfaces\IObject|null $owner
     * @return IService
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function install($config = [], $properties = [], IObject $owner = null): IService
    {
        static::beforeInstallService($config, $properties);
        static::init($config);
        $service = static::it($properties, $owner);
        $service->afterInstallService();
        return $service;
    }

    /**
     * Инициализация сервиса
     *
     * @param array|string|\Closure $config
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function init($config = [])
    {
        if (!static::$_initialized) {
            if ($config instanceof \Closure) {
                $config = $config();
            }
            if (is_string($config)) {
                $configFile = Core::resolvePath($config) . '.php';
                if (!file_exists($configFile) || !is_readable($configFile))
                    throw self::exceptionService('Configuration file <{configFile}> not found', ['configFile' => $configFile]);
                $config = require $configFile;
            }
            if (!is_array($config))
                throw self::exceptionService('Invalid service configuration');
            static::$_config = array_replace_recursive(static::$_config, $config);
            static::$_initialized = true;
        }
    }

    /**
     * Получение экземпляра сервиса
     *
     * @param array|string|\Closure $properties
     * @param \gear\interfaces\IObject|null $owner
     * @return IService
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function it($properties = [], IObject $owner = null): IService
    {
        return new static($properties, $owner);
    }


    /**
     * Деинсталляция сервиса
     *
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstall()
    {
        $this->uninstallService();
    }

    /**
     * Генерация события onBeforeInstallService перед установкой сервиса
     *
     * @param array $config
     * @param array $properties
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function beforeInstallService($config, $properties)
    {
        return Core::trigger('onBeforeInstallService', new GEvent(static::class, ['config' => &$config, 'properties' => &$properties]));
    }

    /**
     * Генерация события onAfterInstallService после процедуры установки сервиса
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterInstallService()
    {
        return Core::trigger('onAfterInstallService', new GEvent($this, ['target' => $this]));
    }

    /**
     * Генерация события onAfterUninstallService после процедуры деинсталляции сервиса
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstallService()
    {
        return Core::trigger('onUninstallService', new GEvent($this, ['target' => $this]));
    }
}