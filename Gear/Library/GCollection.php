<?php

namespace Gear\Library;

/**
 * Класс коллекций
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.2
 * @version 0.0.2
 */
class GCollection implements \Iterator, \ArrayAccess, \Countable
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected $_items = [];
    /* Public */

    /**
     * Получение значение коллекци по соответствующему ключу
     *
     * @param string $name
     * @return mixed|null
     * @since 0.0.2
     * @version 0.0.2
     */
    public function __get(string $name)
    {
        return isset($this->_items[$name]) ? $this->_items[$name] : null;
    }

    /**
     * Установка значения в коллекцию
     *
     * @param $name
     * @param $value
     * @since 0.0.2
     * @version 0.0.2
     */
    public function __set($name, $value)
    {
        $this->_items[$name] = $value;
    }

    /**
     * Добавление элемента(ов) в конец коллекции
     *
     * @param mixed ...$values
     * @return GCollection
     * @since 0.0.2
     * @version 0.0.2
     */
    public function add(...$values): GCollection
    {
        return $this->push(...$values);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->_items);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->_items);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        key($this->_items);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        next($this->_items);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->_items);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return array_key_exists($offset, $this->_items) ? $this->_items[$offset] : null;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if ($offset) {
            $this->_items[$offset] = $value;
        } else {
            $this->_items[] = $value;
        }
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->_items[$offset]);
    }

    /**
     * Удаляет и возвращает последний элемент из коллекции
     *
     * @return mixed
     * @since 0.0.2
     * @version 0.0.2
     */
    public function pop()
    {
        return array_pop($this->_items);
    }

    /**
     * Добавление элемента(ов) в конец коллекции
     *
     * @param mixed ...$values
     * @return GCollection
     * @since 0.0.2
     * @version 0.0.2
     */
    public function push(...$values): GCollection
    {
        array_push($this->_items, ...$values);
        return $this;
    }

    /**
     * Удаление элемента
     *
     * @param mixed $value
     * @return GCollection
     * @since 0.0.2
     * @version 0.0.2
     */
    public function remove($value): GCollection
    {
        foreach ($this->_items as $key => $v) {
            if ($v === $value) {
                unset($this->_items[$key]);
            }
        }
        return $this;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->_items);
    }

    /**
     * Удаляет и возвращает первый элемент из коллекции
     *
     * @return mixed
     * @since 0.0.2
     * @version 0.0.2
     */
    public function shift()
    {
        return array_shift($this->_items);
    }

    /**
     * Добавление элемента(ов) в начало коллекции
     *
     * @param mixed ...$values
     * @return GCollection
     * @since 0.0.2
     * @version 0.0.2
     */
    public function unshift(...$values): GCollection
    {
        array_unshift($this->_items, ...$values);
        return $this;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return !key($this->_items);
    }
}
