<?php

namespace Gear\Interfaces;

/**
 * Интерфейс коллекций
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
interface CollectionInterface extends \Iterator
{
    /**
     * Добавление элемента(ов) в конец коллекции
     *
     * @param mixed ...$values
     * @return CollectionInterface
     * @since 0.0.2
     * @version 0.0.2
     */
    public function add(...$values): CollectionInterface;

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count();

    /**
     * Возвращает первый элемент массива
     *
     * @return mixed
     * @since 0.0.2
     * @version 0.0.2
     */
    public function first();

    /**
     * Возвращает последний элемент массива
     *
     * @return mixed
     * @since 0.0.2
     * @version 0.0.2
     */
    public function last();

    /**
     * Удаляет и возвращает последний элемент из коллекции
     *
     * @return mixed
     * @since 0.0.2
     * @version 0.0.2
     */
    public function pop();

    /**
     * Добавление элемента(ов) в конец коллекции
     *
     * @param mixed ...$values
     * @return CollectionInterface
     * @since 0.0.2
     * @version 0.0.2
     */
    public function push(...$values): CollectionInterface;

    /**
     * Удаление элемента
     *
     * @param mixed $value
     * @return CollectionInterface
     * @since 0.0.2
     * @version 0.0.2
     */
    public function remove($value): CollectionInterface;

    /**
     * Удаляет и возвращает первый элемент из коллекции
     *
     * @return mixed
     * @since 0.0.2
     * @version 0.0.2
     */
    public function shift();

    /**
     * Добавление элемента(ов) в начало коллекции
     *
     * @param mixed ...$values
     * @return CollectionInterface
     * @since 0.0.2
     * @version 0.0.2
     */
    public function unshift(...$values): CollectionInterface;
}
