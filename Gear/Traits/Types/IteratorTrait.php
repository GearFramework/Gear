<?php

namespace Gear\Traits\Types;

/**
 * Трейт для внешних итераторов или объектов, которые могут повторять себя изнутри
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
trait IteratorTrait
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Возвращает текущий элемент
     *
     * @return mixed
     */
    public function current(): mixed
    {
        return current($this->items);
    }

    /**
     * Перемещает внутренний указатель на следующий элемент
     *
     * @return void
     */
    public function next(): void
    {
        next($this->items);
    }

    /**
     * Возвращает индекс текущего элемента
     *
     * @return int|string|null
     */
    public function key(): null|int|string
    {
        return key($this->items);
    }

    /**
     * Проверяет корректность текущей позиции
     *
     * @return bool
     */
    public function valid(): bool
    {
        return key($this->items) !== null;
    }

    /**
     * Перемещает внутренний указатель в начало списка
     *
     * @return void
     */
    public function rewind(): void
    {
        reset($this->items);
    }
}
