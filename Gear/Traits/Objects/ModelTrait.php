<?php

namespace Gear\Traits\Objects;

use Gear\Core;
use Gear\Interfaces\Objects\ModelInterface;

/**
 * Трейт базовых объектов
 *
 * @property ModelInterface $owner
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
trait ModelTrait
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Установка владельца объекта
     *
     * @param   ModelInterface|null $owner
     * @return  void
     */
    public function setOwner(?ModelInterface $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * Возвращает владельца объекта или null если объект такого не имеет
     *
     * @return ModelInterface|null
     */
    public function getOwner(): ?ModelInterface
    {
        return $this->owner;
    }

    /**
     * Получение/установка конфигурационных параметров класса
     *
     * @param   null|string $name
     * @param   mixed       $value
     * @return  mixed
     */
    public static function i(?string $name = null, mixed $value = null): mixed
    {
        if ($name === null && $value === null) {
            return static::$config;
        }
        if ($name && $value === null) {
            if (is_array($name)) {
                static::$config = array_replace_recursive(static::$config, $name);
                return true;
            }
            if (isset(static::$config[$name])) {
                return static::$config[$name];
            }
            if (isset(self::$config[$name])) {
                return self::$config[$name];
            }
            return null;
        }
        if ($name && $value) {
            static::$config[$name] = $value;
        }
        return null;
    }

    /**
     * Возвращает название пространства имён класса без названия самого класса
     *
     * @return string
     */
    public static function getNamespace(): string
    {
        return Core::getNamespace(static::class);
    }
}
