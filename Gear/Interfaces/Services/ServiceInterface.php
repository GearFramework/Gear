<?php

namespace Gear\Interfaces\Services;

use Gear\Interfaces\Objects\ModelInterface;

/**
 * Интерфейс сервисов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface ServiceInterface extends ModelInterface
{
    /**
     * Установка сервиса
     *
     * @param   array               $config
     * @param   array               $properties
     * @param   ModelInterface|null $owner
     * @return  ServiceInterface
     */
    public static function install(
        array $config = [],
        array $properties = [],
        ModelInterface $owner = null
    ): ServiceInterface;

    /**
     * Создание сервиса
     *
     * @param   array               $properties
     * @param   ModelInterface|null $owner
     * @return  ServiceInterface
     */
    public static function it(array $properties = [], ?ModelInterface $owner = null): ServiceInterface;

    /**
     * Деинсталляция сервиса
     *
     * @return void
     */
    public function uninstall(): void;
}
