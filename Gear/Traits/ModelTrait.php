<?php

namespace Gear\Traits;

trait ModelTrait
{
    /**
     * Возвращает значение поля, которое является первичным ключом
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getPrimaryKey()
    {
        return $this->props(static::getPrimaryKeyName());
    }

    /**
     * Возвращает название поля, которое является первичным ключом
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function getPrimaryKeyName(): string
    {
        return static::$primaryKeyName;
    }

    /**
     * Устанавливает значение для поля, которое является первичным ключом
     *
     * @param mixed $value
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setPrimaryKey($value)
    {
        $this->props(static::getPrimaryKeyName(), $value);
    }

    /**
     * Устанавливает название поля, которое является первичным ключом
     *
     * @param string $pkName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public static function setPrimaryKeyName(string $pkName)
    {
        static::$primaryKeyName = $pkName;
    }
}
