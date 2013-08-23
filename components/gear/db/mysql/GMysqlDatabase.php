<?php

namespace gear\components\gear\db\mysql;
use \gear\Core;
use \gear\library\db\GDbDatabase;
use \gear\library\GException;

/** 
 * MySQL база данных
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 11.06.2013
 */
class GMysqlDatabase extends GDbDatabase
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'classItem' => '\\gear\\components\\gear\\db\\mysql\\GMysqlCollection',
    );
    protected $_items = array();
    protected $_current = null;
    /* Public */
    
    /**
     * Выбор текущей базы данных
     * 
     * @access public
     * @return $this
     */
    public function select()
    {
        if (!@mysqli_select_db($this->getHandler(), $this->name))
            $this->e($this->error());
        return $this;
    }
    
    /**
     * Удаление базы данных
     *
     * @access public 
     * @return $this
     */
    public function drop()
    {
        if (!@mysqli_query($this->getHandler(), 'DROP DATABASE `' . $this->name . '`'))
            $this->e($this->error());
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
     * Перемотка в начало списка таблиц
     * 
     * @access public
     * @return GMySqlCollection
     */
    public function rewind()
    {
        $this->event('onBeforeRewind');
        $result = mysqli_query($this->getHandler(), 'SHOW TABLES');
        $class = $this->i('classItem');
        while($collection = mysqli_fetch_row($result))
        {
            $this->_items[$collection[0]] = new $class(array('owner' => $this, 'name' => $collection[0]));
        }
        $this->event('onAfterRewind');
        return $this->_current = reset($this->_items);
    }
}

/** 
 * Исключения базы данных
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 11.06.2013
 */
class MysqlDatabaseException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
