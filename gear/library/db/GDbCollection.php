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
    /* Traits */
    use \gear\traits\TFactory;
    /* Const */
    /* Private */
    /* Protected */
    protected $_cursor = null;
    protected $_lastInsertId = 0;
    /* Public */
    
    /**
     * Установка текущего запроса выборки из коллекции
     *
     * @access public
     * @param object $cursor
     * @return object
     */
    public function setCursor($cursor)
    {
        $this->_cursor = $cursor;
        return $this;
    }

    /**
     * Возвращает текущий запрос выборки из коллекции
     *
     * @access public
     * @return object
     */
    public function getCursor() { return $this->_cursor; }
    
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
     * Поиск элементов по заданному критерию
     *
     * @access public
     * @param null|array $criteria
     * @param null|array $fields
     * @return GDbCursor
     */
    abstract public function find($criteria = null, $fields = null);

    /**
     * Возвращает один элемент соответствующий указанному критерию
     *
     * @access public
     * @param null|array $criteria
     * @param null|array $fields
     * @return GDbCursor
     */
    public function findOne($criteria = null, $fields = null)
    {
        $this->cursor = $this->factory();
        return $this->cursor->findOne($criteria, $fields);
    }

    /**
     * Возвращает первые N элементов из коллекции
     *
     * @access public
     * @param int $count
     * @return GDbCursor
     */
    public function first($count = 1) { return $this->find()->limit((int)$count); }

    /**
     * Перемотка текущей коллекции в начало
     *
     * @access public
     * @return mixed
     */
    public function rewind()
    {
        if (!$this->current)
            $this->cursor = $this->factory();
        $this->cursor->find()->rewind();
    }

    /**
     * Текущая элемент в списке
     * 
     * @access public
     * @return array
     */
    public function current() { return $this->cursor->current(); }
    
    /**
     * Следующий элемент из списка
     * 
     * @access public
     * @return void
     */
    public function next() { return $this->cursor->next(); }
    
    /**
     * Возвращает true если текущий элемент списка является валидной записью
     * 
     * @access public
     * @return boolean
     */
    public function valid() { return $this->cursor->valid(); }
    
    /**
     * Возвращает ключ текущего элемента
     * 
     * @access public
     * @return integer
     */
    public function key() { return $this->cursor->key(); }

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

