<?php

namespace gear\library\db;
use \gear\Core;
use \gear\library\GModel;
use \gear\library\GException;

/** 
 * Таблица
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 04.08.2013
 */
abstract class GDbCollection extends GModel implements \Iterator
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_current = null;
    /* Public */
    
    public function __call($name, $args)
    {
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
        $this->_current = new $class($properties);
        return call_user_func_array(array($this->_current, $name), $args);
    }
    
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
    
    public function rewind()
    {
        if (!$this->_current)
        {
            list($class, $config, $properties) = Core::getRecords($this->i('classItem'));
            $properties['owner'] = $this;
            $this->_current = new $class($properties);
        }
        return $this->_current->find()->rewind();
    }

    /**
     * Текущая элемент в списке
     * 
     * @access public
     * @return GDbDatabase
     */
    public function current() { return $this->_current->current(); }
    
    /**
     * Следующий элемент из списка
     * 
     * @access public
     * @return GDbDatabase
     */
    public function next() { return $this->_current->next(); }
    
    /**
     * Возвращает true если текущий элемент списка является валидной записью
     * 
     * @access public
     * @return boolean
     */
    public function valid() { return $this->_current->valid(); }
    
    /**
     * Возвращает ключ текущего элемента
     * 
     * @access public
     * @return integer
     */
    public function key() { return $this->_current->key(); }

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
     * Возвращает базу данных, в которой находится коллекция курсора
     * 
     * @access public
     * @return \gear\library\db\GDbDatabase
     */
    public function getDatabase()
    {
        return $this->_owner;
    }
    
    /**
     * Возвращает соединение с сервером базы данных
     * 
     * @access public
     * @return \gear\library\db\GDbConnection
     */
    public function getConnection()
    {
        return $this->_owner->getConnection();
    }
}

/** 
 * Исключения коллекции
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 04.08.2013
 */
class DbCollectionException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
