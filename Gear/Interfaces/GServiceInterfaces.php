<?php

namespace Gear\Interfaces;

/**
 * Интерфейс сервисов (модуль, компонент, плагин)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface GServiceInterface
{
    /**
     * Инициализация сервиса
     *
     * @param array|string $config
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function init($config = []): bool;

    /**
     * Установка сервиса
     *
     * @param array|string $config
     * @param array|string $properties
     * @param GObjectInterface|null $owner
     * @return GServiceInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public static function install($config = [], $properties = [], ?GObjectInterface $owner = null): GServiceInterface;

    /**
     * Получение экземпляра сервиса
     *
     * @param array|string $properties
     * @param GObjectInterface|null $owner
     * @return GServiceInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public static function it($properties = [], ?GObjectInterface $owner = null): GServiceInterface;

    /**
     * Деинсталляция сервиса
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function uninstall();
}

/**
 * Интерфейс модулей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface GModuleInterface extends GServiceInterface {}

/**
 * Интерфейс компонентов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface GComponentInterface extends GServiceInterface {}

/**
 * Интерфейс плагинов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface GPluginInterface extends GServiceInterface {}

/**
 * Интерфейс хелперов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface GHelperInterface
{
    /**
     * Обработка и выполнение вызываемого метода
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @since 0.0.2
     * @version 0.0.1
     */
    public static function __callStatic(string $name, array $arguments);

    /**
     * Запуск метода хелпера
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function __call(string $name, array $arguments);
}
