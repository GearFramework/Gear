<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GCollection;
use \gear\library\GException;

/** 
 * 
 * 
 *  
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 23.08.2013
 */
class GProxyCollection extends GCollection
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_source = null;
    /* Public */
    
    /**
     * Подключение источника данных
     * 
     * @access public
     * @param object $source
     * @return $this
     */
    public function attach(\Iterator $source)
    {
        $this->_source = $source;
        return $this;
    }
    
    public function factory($properties)
    {
        try
        {
            $object = $this->getOwner()->factory($properties);
            if (!$object)
                $this->e('Invalid factory');
            return $object;
        }
        catch(\Exception $e)
        {
            return $this->next();
        }
    }
    
    /**
     * Установка указателя на начало массива
     *
     * @access public
     * @return mixed
     */
    public function rewind()
    {
        $this->_source->rewind();
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
        return $properties ? $this->factory($properties) : null;
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
        $this->_source->next();
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
