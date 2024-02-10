<?php

namespace Gear\Traits\Types;

/**
 * Трейт для классов, реализующие интерфейс Countable и один из классов
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
trait CountableTrait
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Возвращает кол-во элементов в списке
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }
}