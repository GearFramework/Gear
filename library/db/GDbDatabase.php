<?php

namespace gear\library\db;
use \gear\Core;
use \gear\library\GModel;
use \gear\library\GException;

/** 
 * База данных
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 04.08.2013
 */
abstract class GDbDatabase extends GModel implements \Iterator
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_current = null;
    protected $_items = array();
    /* Public */

    /**
     * Выбор текущей базы данных
     * 
     * @abstract
     * @access public
     * @return $this
     */
    abstract public function select();

    /**
     * Удаление базы данных
     *
     * @abstract
     * @access public 
     * @return $this
     */
    abstract public function drop();
    
    /**
     * Получение текста последней ошибки
     * 
     * @abstract
     * @access public
     * @return string
     */
    abstract public function error();
    
    /**
     * выбор указанной таблицы
     * 
     * @access public
     * @param string $name
     * @return GDbCollection
     */
    public function selectCollection($name)
    {
        if ($this->_current && $this->_current->name === $name)
            return $this->_current;
        else
        if (isset($this->_items[$name]))
            return $this->_current = $this->_items[$name];
        else
        {
            list($class, $config, $properties) = Core::getRecords($this->i('classItem'));
            return $this->_current = $this->_items[$name] = new $class(array_merge($properties, array('owner' => $this, 'name' => $name)));
        }
    }
    
    /**
     * Текущая коллекция в списке
     * 
     * @access public
     * @return GDbDatabase
     */
    public function current() { return $this->_current = current($this->_items); }
    
    /**
     * Следующая коллекция из списка
     * 
     * @access public
     * @return GDbDatabase
     */
    public function next() { return $this->_current = next($this->_items); }
    
    /**
     * Возвращает true если текущий элемент списка является коллекцией
     * 
     * @access public
     * @return boolean
     */
    public function valid() { return current($this->_items) !== false ? true : false; }
    
    /**
     * Возвращает ключ текущего элемента
     * 
     * @access public
     * @return integer
     */
    public function key() { return key($this->_items); }

    /**
     * Возвращает ресурс соединения с сервером базы данных
     * 
     * @access public
     * @return mixed
     */
    public function getHandler()
    {
        return $this->_owner->getHandler();
    }
    
    /**
     * Возвращает соединение с сервером базы данных
     * 
     * @access public
     * @return \gear\library\db\GDbConnection
     */
    public function getConnection()
    {
        return $this->_owner;
    }

    /**
     * Обработчик события onConstructed вызываемого при создании
     * экземпляра класса
     * 
     * @access public
     * @param GEvent $event
     * @return void
     */
    public function onConstructed()
    {
        $this->select();
    }
}

/** 
 * Исключения базы данных
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 04.08.2013
 */
class DbDatabaseException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
