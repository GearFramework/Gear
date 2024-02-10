<?php

namespace Gear\Traits\Types;

/**
 * Трейт обеспечивает доступ к объектам в виде массивов
 *
 * @property array items
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2023 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 3.0.0
 * @version 3.0.0
 */
trait ArrayAccessTrait
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Получение элемента из контейнера по ключу
     *
     * @param   mixed $offset
     * @return  mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return array_key_exists($offset, $this->items) ? $this->items[$offset] : null;
    }

    /**
     * Установка в коллекцию элемента с соответствующим ключем
     *
     * @param   string $offset
     * @param   mixed  $value
     * @return  void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->items[$offset] = $value;
    }

    /**
     * Проверка наличия элемента в коллекции с соответствующим ключём
     *
     * @param   mixed $offset
     * @return  bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * Удаление из коллекции элемента с соответствующим ключём
     *
     * @param   mixed $offset
     * @return  void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }
}
