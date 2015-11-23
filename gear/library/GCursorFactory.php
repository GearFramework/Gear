<?php

namespace gear\library;

use gear\Core;
use gear\library\GPlugin;
use gear\library\GException;

/**
 * Плагин курсора ленивого создания объектов из коллекции
 *
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 10.11.2015
 * @php 5.4.x or higher
 * @release 1.0.0
 */
class GCursorFactory extends GPlugin implements \Iterator
{
    /* Traits */
    /* Const */
    /* Private */
    /* Protected */
    protected static $_init = false;
    protected $_collection = null;
    /* Public */

    /**
     * Фабрика объектов
     *
     * @access public
     * @param array $properties
     * @return null|object
     */
    public function factory($properties)
    {
        return !is_array($properties) ? null : (!($object = $this->owner->factory($properties)) ? $this->next() : $object);
    }

    /**
     * Установка коллекции элементов, из которой будут браться данные для фабрики объектов
     *
     * @access public
     * @param \Iterator $collection
     * @return $this
     */
    public function setCollection(\Iterator $collection)
    {
        $this->_collection = $collection;
        return $this;
    }

    /**
     * Получение коллекции элементов, из которой берутся данные для фабрики объектов
     *
     * @access public
     * @return \Iterator $collection
     */
    public function getCollection() { return $this->_collection; }

    /**
     * Возвращает текущий элемент
     *
     * @access public
     * @return object
     */
    public function current() { return $this->factory($this->collection->curren()); }

    /**
     * Перемещает курсор на следующий элемент
     *
     * @access public
     * @return object
     */
    public function next() { $this->collection->next(); }

    /**
     * Возвращает ключ элемента
     *
     * @access public
     * @return mixed
     */
    public function key() { $this->collection->key(); }

    /**
     * Возвращает: false если достигнут конец коллекции, иначе true
     *
     * @access public
     * @return object
     */
    public function valid() { return $this->collection->valid(); }

    /**
     * Перемещение курсора в начало коллекции
     *
     * @access public
     * @return object
     */
    public function rewind() { $this->collection->rewind(); }
}
