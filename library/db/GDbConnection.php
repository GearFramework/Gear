<?php

namespace gear\library\db;
use \gear\Core;
use \gear\library\GComponent;
use \gear\library\GException;

/** 
 * Класс компонента выполняющего подключение к базе данных
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 04.08.2013
 */
abstract class GDbConnection extends GComponent implements \Iterator
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array
    (
        'autoConnect' => true,
    );
    protected static $_init = false;
    protected $_handler = null;
    protected $_items = array();
    protected $_current = null;
    /* Public */
    
    /**
     * Подключение к серверу баз данных
     * 
     * @access public
     * @abstract
     * @return $this
     */
    abstract public function connect();
    
    /**
     * Завершение соединения с сервером баз данных
     * 
     * @access public
     * @abstract
     * @return $this
     */
    abstract public function close();
    
    /**
     * Возвращает true если соединение уже установлено, иначе false
     * 
     * @access public
     * @return boolean
     */
    public function isConnected() { return $this->_handler ? true : false; }
    
    /**
     * Выполняет подключение к серверу, если соединение ещё не было
     * установлено
     * 
     * @access public
     * @return $this
     */
    public function reconnect()
    {
        if (!$this->isConnected())
            $this->connect();
        return $this;
    }
        
    /**
     * Выбор указанной базы данных
     * 
     * @access public
     * @param string $name
     * @return GDbDatabase
     */
    public function selectDB($name)
    {
        if ($this->_current && $this->_current->name === $name)
            return $this->_current;
        else
        if (isset($this->_items[$name]))
            return $this->_current = $this->_items[$name];
        else
        {
            $class = $this->i('classItem');
            return $this->_current = $this->_items[$name] = new $class(array('owner' => $this, 'name' => $name));
        }
    }
    
    /**
     * Выбор указанной базы данных и таблицы
     * 
     * @access public
     * @param string $dbName
     * @param string $collectionName
     * @return GDbCollection
     */
    public function selectCollection($dbName, $collectionName)
    {
        return $this->selectDB($dbName)->selectCollection($collectionName);
    }
    
    /**
     * Текущая база данных в списке
     * 
     * @access public
     * @return GDbDatabase
     */
    public function current() { return $this->_current = current($this->_items); }
    
    /**
     * Следующая база данных из списка
     * 
     * @access public
     * @return GDbDatabase
     */
    public function next() { return $this->_current = next($this->_items); }
    
    /**
     * Возвращает true если текущий элемент списка является базой данных
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
    public function getHandler() { return $this->_handler; }
    
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
        if ($this->i('autoConnect'))
            $this->connect();
    }
}

/** 
 * Исключения компонента выполняющего подключение к базе данных
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 04.08.2013
 */
class DbConnectionException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
