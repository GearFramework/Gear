<?php

namespace Gear\Traits;

/**
 * Трэйт для моделей
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
trait ModelTrait
{
    /**
     * Возвращает значение поля, которое является первичным ключом
     *
     * @return mixed
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getPrimaryKey()
    {
        return $this->{static::getPrimaryKeyName()};
    }

    /**
     * Возвращает название поля, которое является первичным ключом
     *
     * @return string
     * @since 0.0.2
     * @version 0.0.2
     */
    public function getPrimaryKeyName(): string
    {
        return static::$primaryKeyName;
    }

    /**
     * Устанавливает значение для поля, которое является первичным ключом
     *
     * @param mixed $value
     * @return void
     * @since 0.0.2
     * @version 0.0.2
     */
    public function setPrimaryKey($value)
    {
        $this->{static::getPrimaryKeyName()} = $value;
    }

    /**
     * Устанавливает название поля, которое является первичным ключом
     *
     * @param string $pkName
     * @return void
     * @since 0.0.2
     * @version 0.0.2
     */
    public function setPrimaryKeyName(string $pkName)
    {
        static::$primaryKeyName = $pkName;
    }
}
