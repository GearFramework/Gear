<?php

namespace Gear\Interfaces;

/**
 * Интерфейс сервисов (модуль, компонент, плагин)
 *
 * @package Gear Framework 2
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface ServiceInterface
{
    /**
     * Генерация события onBeforeInstallService перед установкой сервиса
     *
     * @param array $config
     * @param array $properties
     * @return mixed
     * @since 0.0.1
     * @version 2.0.0
     */
    public static function beforeInstallService(array $config, array $properties): mixed;

    /**
     * Генерация события onAfterInstallService после процедуры установки сервиса
     *
     * @return mixed
     * @since 0.0.1
     * @version 2.0.0
     */
    public function afterInstallService(): mixed;

    /**
     * Инициализация сервиса
     *
     * @param array|string $config
     * @return vold
     * @since 0.0.1
     * @version 2.0.0
     */
    public static function init(array|string $config = []): void;

    /**
     * Установка сервиса
     *
     * @param array $config
     * @param array $properties
     * @param ObjectInterface|null $owner
     * @return ServiceInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    public static function install(
        array $config = [],
        array $properties = [],
        ?ObjectInterface $owner = null
    ): ServiceInterface;

    /**
     * Получение экземпляра сервиса
     *
     * @param array $properties
     * @param ObjectInterface|null $owner
     * @return ServiceInterface
     * @since 0.0.1
     * @version 2.0.0
     */
    public static function it($properties = [], ?ObjectInterface $owner = null): ServiceInterface;

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
 * @package Gear Framework 2
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface ModuleInterface extends ServiceInterface {}

/**
 * Интерфейс компонентов
 *
 * @package Gear Framework 2
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface ComponentInterface extends ServiceInterface {}

/**
 * Интерфейс плагинов
 *
 * @package Gear Framework 2
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface PluginInterface extends ServiceInterface {}

/**
 * Интерфейс хелперов
 *
 * @package Gear Framework 2
 * @author Kukushkin Denis
 * @copyright 2022 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 2.0.0
 */
interface HelperInterface
{
    /**
     * Обработка и выполнение вызываемого метода
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @since 0.0.2
     * @version 2.0.0
     */
    public static function __callStatic(string $name, array $arguments);

    /**
     * Запуск метода хелпера
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @since 0.0.1
     * @version 2.0.0
     */
    public function __call(string $name, array $arguments);
}
