<?php

namespace Gear\Interfaces\Objects;

/**
 * Интерфейс модели с фиксированной схемой свойств
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface SchemaInterface
{
    /**
     * Возвращает схему модели
     *
     * @return array
     */
    public function getSchema(): array;

    /**
     * Возвращает названия свойств модели
     *
     * @return array
     */
    public function getSchemaNames(): array;

    /**
     * Возвращает значения свойств модели
     *
     * @return array
     */
    public function getSchemaValues(): array;
}
