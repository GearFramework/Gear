<?php

namespace gear\components\gear\db\mysql;
use \gear\Core;
use \gear\library\db\GDbCollection;
use \gear\library\GException;

/** 
 * MySQL таблица
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 11.06.2013
 */
class GMysqlCollection extends GDbCollection
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'classItem' => '\\gear\\components\\gear\\db\\mysql\\GMysqlCursor',
    );
    /* Public */
    
    /**
     * Удаление таблицы
     * 
     * @access public
     * @return $this
     */
    public function drop()
    {
        if (!@mysqli_query($this->getHandler(), 'DROP TABLE `' . $this->name . '`'))
            $this->e($this->error());
        return $this;
    }
    
    /**
     * Очистка таблицы
     * 
     * @access public
     * @return $this
     */
    public function truncate()
    {
        if (!@mysqli_query($this->getHandler(), 'TRUNCATE TABLE `' . $this->name . '`'))
            $this->e($this->error());
        return $this;
    }
    
    /**
     * Получение текста последней ошибки
     * 
     * @access public
     * @return string
     */
    public function error()
    {
        return mysqli_error($this->getHandler());
    }
    
    /**
     * Перемотка в начало таблицы
     * 
     * @access public
     * @return mixed
     */
    public function rewind()
    {
        $this->event('onBeforeRewind');
        if (!$this->_current)
        {
            $class = $this->i('classItem');
            $this->_current = new $class(array('owner' => $this));
            $this->_current->find();
        }
        $this->event('onBeforeRewind');
        return $this->_current->rewind();
    }
}

/** 
 * Исключения коллекции
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 11.06.2013
 */
class MySqlCollectionException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
