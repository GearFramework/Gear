<?php

namespace gear\library\db;
use \gear\Core;
use \gear\library\GModel;

/** 
 * Таблица
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 04.08.2013
 * @php 5.4.x
 * @release 1.0.0
 */
abstract class GDbCollection extends GModel implements \Iterator
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_current = null;
    protected $_lastInsertId = 0;
    /* Public */
    
    public function __call($name, $args)
    {
        if (preg_match('/^exception/', $name))
            return call_user_func_array(array(Core, $name), $args);
        if (preg_match('/^on[A-Z]/', $name))
        {
            array_unshift($args, $name);
            return call_user_func_array(array($this, 'event'), $args);
        }
        if (isset($this->_behaviors[$name]) && is_callable($this->_behaviors[$name]))
            return call_user_func_array($this->_behaviors[$name], $args);
        else
        {
            foreach($this->_behaviors as $b)
            {
                if (!($b instanceof Closure) && method_exists($b, $name))
                    return call_user_func_array(array($b, $name), $args);
            }
        }
        if ($this->isPluginRegistered($name))
        {
            $p = $this->p($name);
            if (is_callable($p))
                return call_user_func_array($p, $args);
        }
        list($class, $config, $properties) = Core::getRecords($this->i('classItem'));
        $properties['owner'] = $this;
        $this->current = new $class($properties);
        return call_user_func_array([$this->_current, $name], $args);
    }

    /**
     * Установка текущего запроса выборки из коллекции
     *
     * @access public
     * @param object $cursor
     * @return object
     */
    public function setCurrent($cursor)
    {
        $this->_current = $cursor;
        return $this;
    }

    /**
     * Возвращает текущий запрос выборки из коллекции
     *
     * @access public
     * @return object
     */
    public function getCurrent() { return $this->_current; }
    
    /**
     * Удаление таблицы
     * 
     * @abstract
     * @access public
     * @return $this
     */
    abstract public function drop(); 
    
    /**
     * очистка таблицы от записей
     * 
     * @abstract
     * @access public
     * @return $this
     */
    abstract public function truncate(); 

    /**
     * Получение текста последней ошибки
     * 
     * @abstract
     * @access public
     * @return string
     */
    abstract public function error();

    /**
     * Перемотка текущей коллекции в начало
     *
     * @access public
     * @return mixed
     */
    public function rewind()
    {
        if (!$this->current)
        {
            list($class, $config, $properties) = Core::getRecords($this->i('classItem'));
            $properties['owner'] = $this;
            $this->current = new $class($properties);
        }
        return $this->current->find()->rewind();
    }

    /**
     * Текущая элемент в списке
     * 
     * @access public
     * @return GDbDatabase
     */
    public function current() { return $this->current->current(); }
    
    /**
     * Следующий элемент из списка
     * 
     * @access public
     * @return GDbDatabase
     */
    public function next() { return $this->current->next(); }
    
    /**
     * Возвращает true если текущий элемент списка является валидной записью
     * 
     * @access public
     * @return boolean
     */
    public function valid() { return $this->current->valid(); }
    
    /**
     * Возвращает ключ текущего элемента
     * 
     * @access public
     * @return integer
     */
    public function key() { return $this->current->key(); }

    /**
     * Возвращает ресурс соединения с сервером базы данных
     * 
     * @access public
     * @return mixed
     */
    public function getHandler() { return $this->owner->getHandler(); }
    
    /**
     * Возвращает базу данных, в которой находится коллекция курсора
     * 
     * @access public
     * @return \gear\library\db\GDbDatabase
     */
    public function getDatabase() { return $this->owner; }
    
    /**
     * Возвращает соединение с сервером базы данных
     * 
     * @access public
     * @return \gear\library\db\GDbConnection
     */
    public function getConnection() { return $this->owner->getConnection(); }
}

