<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GCollection;
use \gear\library\GException;

class GProxyCollection extends GCollection
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_source = null;
    /* Public */
    
    public function attach($source)
    {
        $this->_source = $source;
        return $this;
    }
    
    /**
     * Установка указателя на начало массива
     *
     * @access public
     * @return mixed
     */
    public function rewind()
    {
        $properties = $this->_source->rewind();
        return $properties ? $this->getOwner()->factory($properties) : null;
    }

    /**
     * Получение значения текущего элемента
     *
     * @access public
     * @return mixed
     */
    public function current()
    {
        $properties = $this->_source->current();
        return $properties ? $this->getOwner()->factory($properties) : null;
    }

    /**
     * Получение ключа текущего элемента
     *
     * @access public
     * @return mixed
     */
    public function key()
    {
        return $this->_source->key();
    }

    /**
     * Получение следующего элемента массива
     *
     * @access public
     * @return mixed
     */
    public function next()
    {
        $properties = $this->_source->next();
        return $properties ? $this->getOwner()->factory($properties) : null;
    }

    /**
     * Проверка достигнут ли конец массива
     *
     * @access public
     * @return boolean
     */
    public function valid()
    {
        return $this->_source->valid();
    }

    /**
     * Возвращает количество элементов в массиве
     *
     * @access public
     * @return integer
     */
    public function count()
    {
        return $this->_source->count();
    }

    /**
     * Очистка массива
     *
     * @access public
     * @return void
     */
    public function clear()
    {
        $this->_source->clear();
    }

    /**
     * Алиас метода clear()
     *
     * @access public
     * @return void
     */
    public function truncate()
    {
        $this->_source->clear();
    }
}

class ProxyCollectionException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
