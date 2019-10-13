<?php

namespace Gear\Library;

use Gear\Core;
use Gear\Interfaces\ObjectInterface;
use Gear\Interfaces\ServiceInterface;

/**
 * Базовый класс сервисов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GService extends GObject implements ServiceInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [];
    /* Public */

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
     * Инициализация сервиса
     *
     * @param array|string $config
     * @return bool
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function init($config = []): bool
    {
        if (is_string($config) === true) {
            $path = Core::resolvePath($config);
            if (substr($path, -4) !== '.php') {
                $path .= '.php';
            }
            if (file_exists($path) === false) {
                throw static::ServiceInitException();
            }
            $config = include($path);
        }
        if (is_array($config) === true) {
            static::$_config = array_replace_recursive(static::$_config, $config);
        } else {
            throw static::ServiceInitException();
        }
        return true;
    }

    /**
     * Установка сервиса
     *
     * @param array|string $config
     * @param array|string $properties
     * @param ObjectInterface|null $owner
     * @return ServiceInterface
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public static function install($config = [], $properties = [], ObjectInterface $owner = null): ServiceInterface
    {
        static::beforeInstallService($config, $properties);
        static::init($config);
        $service = static::it($properties, $owner);
        $service->afterInstallService();
        return $service;
    }

    /**
     * Получение экземпляра сервиса
     *
     * @param array|string $properties
     * @param ObjectInterface|null $owner
     * @return ServiceInterface
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public static function it($properties = [], ?ObjectInterface $owner = null): ServiceInterface
    {
        if (is_string($properties) === true) {
            $path = Core::resolvePath($properties);
            if (substr($path, -4) !== '.php') {
                $path .= '.php';
            }
            if (file_exists($path) === false) {
                throw static::ServiceConstructException();
            }
            $properties = include $path;
        }
        if (is_array($properties) === false) {
            throw static::ServiceConstructException();
        }
        $service = new static($properties, $owner);
        return $service;
    }

    /**
     * Деинсталляция сервиса
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstall()
    {
        $this->uninstallService();
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