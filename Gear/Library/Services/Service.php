<?php

namespace Gear\Library\Services;

use Gear\Interfaces\Objects\ModelInterface;
use Gear\Interfaces\Services\ServiceInterface;
use Gear\Library\Objects\Model;

/**
 * Класс сервисов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
abstract class Service extends Model implements ServiceInterface
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Установка сервиса
     *
     * @param array                 $config
     * @param array                 $properties
     * @param ModelInterface|null  $owner
     * @return Service
     */
    public static function install(
        array $config = [],
        array $properties = [],
        ModelInterface $owner = null
    ): Service {
        static::init($config);
        $service = static::it($properties);
        $service->setOwner($owner);
        $service->afterInstall();
        return $service;
    }

    /**
     * Инициализация класса сервиса
     *
     * @param array $config
     * @return void
     */
    public static function init(array $config): void
    {
        static::$config = array_replace_recursive(static::$config, $config);
    }

    /**
     * Создание сервиса
     *
     * @param array                 $properties
     * @param ModelInterface|null   $owner
     * @return Service
     */
    public static function it(array $properties = [], ?ModelInterface $owner = null): Service
    {
        return new static($properties, $owner);
    }

    /**
     * Выполняется после установки сервиса
     *
     * @return void
     */
    public function afterInstall(): void {}

    /**
     * Деинсталляция сервиса
     *
     * @return void
     */
    public function uninstall(): void {}
}
