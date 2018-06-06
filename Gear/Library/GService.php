<?php

namespace Gear\Library;

use Gear\Core;
use Gear\Interfaces\IObject;
use Gear\Interfaces\IService;

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
    protected static $_config = [];
    protected static $_isInitialized = false;
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
//        if (static::isInitialized() === false) {
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
            static::setInitialized(true);
        //}
        return static::isInitialized();
    }

    /**
     * Установка сервиса
     *
     * @param array|string $config
     * @param array|string $properties
     * @param \Gear\Interfaces\IObject|null $owner
     * @return IService
     * @throws \CoreException
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
     * Возврашает true, если класс сервиса уже был проинициализирован, иначе возвращает false
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function isInitialized(): bool
    {
        return static::$_isInitialized;
    }

    /**
     * Получение экземпляра сервиса
     *
     * @param array|string $properties
     * @param IObject|null $owner
     * @return IService
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function it($properties = [], IObject $owner = null): IService
    {
        if (is_string($properties) === true) {
            $path = Core::resolvePath($properties);
            if (substr($path, -4) !== '.php') {
                $path .= '.php';
            }
            if (file_exists($path) === false) {
                throw static::ServiceConstructException();
            }
            $properties = include($path);
        }
        if (is_array($properties) === false) {
            throw static::ServiceConstructException();
        }
        $service = new static($properties, $owner);
        return $service;
    }

    /**
     * Устнаваливает true или false для определения инициализации класса сервиса
     *
     * @param bool $value
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function setInitialized(bool $value)
    {
        static::$_isInitialized = $value;
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