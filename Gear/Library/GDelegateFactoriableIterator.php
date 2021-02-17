<?php

namespace Gear\Library;

use Gear\Interfaces\FactoryInterface;
use Gear\Interfaces\ObjectInterface;

/**
 * Библиотека для делегируемой фабрики
 *
 * @package Gear Framework
 *
 * @property FactoryInterface factory
 * @property FactoryInterface owner
 * @property \Iterator source
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GDelegateFactoriableIterator extends GModel implements \Iterator
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected ?iterable $_source = null;
    /* Public */

    /**
     * Возвращает количество элементов в источнике
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function count(): int
    {
        return $this->source->count();
    }

    /**
     * Возвращает фабрику
     *
     * @return FactoryInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getFactory(): FactoryInterface
    {
        return $this->owner;
    }

    /**
     * Возвращает итерируемый источник данных для фабрики
     *
     * @return \Iterator
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getSource(): \Iterator
    {
        return $this->_source;
    }

    /**
     * Установка итерируемого источника данных для фабрики
     *
     * @param \Iterator $source
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setSource(\Iterator $source)
    {
        $this->_source = $source;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        $properties = $this->source->current();
        /** @var ObjectInterface&FactoryInterface $factory */
        $factory = $this->factory;
        return $properties ? $factory->factory($properties, $factory) : null;
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->source->next();
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->source->key();
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
        return $this->source->valid();
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->source->rewind();
    }
}
