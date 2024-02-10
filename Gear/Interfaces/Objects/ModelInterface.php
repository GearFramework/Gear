<?php

namespace Gear\Interfaces\Objects;

/**
 * Интерфейс моделей
 *
 * @property string namespace
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
interface ModelInterface
{
    /**
     * Возвращает владельца объекта или null если объект такого не имеет
     *
     * @return ModelInterface|null
     */
    public function getOwner(): ?ModelInterface;

    /**
     * Получение/установка конфигурационных параметров класса
     *
     * @param   null|string   $name
     * @param   mixed         $value
     * @return  mixed
     */
    public static function i(?string $name = null, mixed $value = null): mixed;

    /**
     * Получение или установка свойств объекта
     *
     * @param   null|string|array $name
     * @param   mixed             $value
     * @return  mixed
     */
    public function props(null|string|array $name = null, mixed $value = null): mixed;

    /**
     * Возвращает название пространства имён класса без названия самого класса
     *
     * @return string
     */
    public static function getNamespace(): string;
}
