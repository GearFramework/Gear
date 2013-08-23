<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GModel;
use \gear\library\GException;

/**
 * Коллекция
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 13.08.2013
 */
class GCollection extends GModel implements \Iterator, \Countable
{
    /* Const */
    /* Private */
    /* Protected */ 
    protected $_items = array();
    /* Public */
    
    /**
     * GCollection::rewind()
     *
     * Установка указателя на начало массива
     *
     * @access public
     * @return mixed
     */
    public function rewind()
    {
        return reset($this->_items);
    }

    /**
     * GCollection::current()
     *
     * Получение значения текущего элемента
     *
     * @access public
     * @return mixed
     */
    public function current()
    {
        return current($this->_items);
    }

    /**
     * GCollection::key()
     *
     * Получение ключа текущего элемента
     *
     * @access public
     * @return mixed
     */
    public function key()
    {
        return key($this->_items);
    }

    /**
     * GCollection::next()
     *
     * Получение следующего элемента массива
     *
     * @access public
     * @return mixed
     */
    public function next()
    {
        return next($this->_items);
    }

    /**
     * GCollection::valid()
     *
     * Проверка достигнут ли конец массива
     *
     * @access public
     * @return boolean
     */
    public function valid()
    {
        return current($this->_items) !== false ? true : false;
    }

    /**
     * GCollection::count()
     *
     * Возвращает количество элементов в массиве
     *
     * @access public
     * @return integer
     */
    public function count()
    {
        return count($this->_items);
    }

    /**
     * GCollection::clear()
     *
     * Очистка массива
     *
     * @access public
     * @return void
     */
    public function clear()
    {
        $this->_items = array();
    }

    /**
     * GCollection::truncate()
     *
     * Алиас метода clear()
     *
     * @access public
     * @return void
     */
    public function truncate()
    {
        $this->clear();
    }
}

/**
 * Обработчик исключений класса GCollection
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 13.08.2013
 */
class CollectionException extends GException 
{
    /* Const */
    /* Private */
    /* Protected */ 
    /* Public */
}
