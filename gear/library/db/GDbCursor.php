<?php

namespace gear\library\db;

use \gear\library\GModel;

/** 
 * Курсор
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 04.08.2013
 * @php 5.3.x
 */
abstract class GDbCursor extends GModel implements \Iterator
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_query = null;
    protected $_resource = null;
    protected $_lastInsertId = 0;
    /* Public */
    
    /**
     * Построение и выполнение полученного запроса
     * 
     * @access public
     * @return $this
     */
    public function query()
    {
        $query = $this->getQuery();
        $this->trigger('onRunQuery', $query);
        if ($this->getConnection()->isPluginRegistered('trace'))
            $this->getConnection()->trace->trace($query);
        if (!$this->runQuery($query))
            $this->exceptionDbCursorQueryError($this->error(), array('query' => $query));
        return $this;
    }
    
    /**
     * Построение запроса
     * 
     * @abstract
     * @access public
     * @return string
     */
    abstract public function buildQuery();
    
    /**
     * Выполнение указанного запроса
     * 
     * @abstract
     * @access public
     * @param string $query
     * @return $this
     */
    abstract public function runQuery($query);
    
    /**
     * Возвращает текст запроса
     * 
     * @access public
     * @return string
     */
    public function getQuery()
    {
        return $this->_query ? $this->_query : $this->buildQuery();
    }

    /**
     * Возвращает текст ошибки
     * 
     * @abstract
     * @access public
     * @return string
     */
    abstract public function error();
    
    /**
     * Формирует поисковый запроса поиска элементов по указанному
     * критерию
     * 
     * @access public
     * @param mixed $criteria
     * @param mixed $fields
     * @return $this
     */
    abstract public function find($criteria = null, $fields = null);
    
    /**
     * Возвращает первую совпавшую запись с указанным критерием.
     * Возвращает ассоциативный массив массив полей и значений согласно
     * указанному аргументу $fields
     * 
     * @access public
     * @param mixed $criteria
     * @param mixed $fields
     * @return array
     */
    public function findOne($criteria = null, $fields = null)
    {
        return $this->find($criteria, $fields)->limit(1)->asAssoc();
    }
    
    /**
     * Добавление в коллекцию новой записи
     * Возвращает количество затронутых строк
     * В случае совпадения PRIMARY KEY генерируется исключение
     * 
     * @abstract
     * @access public
     * @param mixed $properties
     * @return integer
     */
    abstract public function insert($properties);
    
    /**
     * Добавление в коллекцию новой записи. В случае совпадения
     * PRIMARY KEY происходит обновление указанных в $updates полей
     * записи
     * Возвращает колчество затронутых полей
     *
     * @abstract
     * @access public 
     * @param mixed $properties
     * @param mixed $updates
     * @return integer
     */
    abstract public function save($properties, $updates = null);

    /**
     * Обновление указанных полей для записей, соответствующих критерию
     * 
     * @abstract
     * @access public
     * @param null|array $criteria
     * @param array|object $properties
     * @return integer
     */
    abstract public function update($criteria = null, $properties);
    
    /**
     * Удаление записей, соответствующих критерию, либо найденных
     * в результате последнего выполненного SELECT-запроса
     * 
     * @abstract
     * @access public
     * @param null|array $criteria
     * @return integer
     */
    abstract public function remove($criteria = null);
    
    /**
     * Подключение таблицы
     *
     * @abstract 
     * @access public
     * @param string|object $collection
     * @param null|array $criterial
     * @return $this
     */
    abstract public function join($collection, $criteria = null);

    /**
     * Левое подключение таблицы
     * 
     * @abstract
     * @access public
     * @param string|object $collection
     * @param null|array $criterial
     * @return $this
     */
    abstract public function left($collection, $criteria = null);

    /**
     * Правое подключение таблицы
     * 
     * @abstract
     * @access public
     * @param string|object $collection
     * @param null|array $criterial
     * @return $this
     */
    abstract public function right($collection, $criteria = null);

    /**
     * Правое подключение таблицы
     *
     * @abstract
     * @access public
     * @param string|object $collection
     * @param null|array $criterial
     * @return $this
     */
    abstract public function outer($collection, $criteria = null);

    /**
     * Формирование критерия поиска
     *
     * @abstract 
     * @access public
     * @param null|array $criteria
     * @return $this
     */
    abstract public function where($criteria = null);
    
    /**
     * Установка группировки результатов запроса
     *
     * @abstract 
     * @access public
     * @param null|string|array $group
     * @return $this
     */
    abstract public function group($group = null);
    
    /**
     * Установка сортировки результатов запроса
     * 
     * @abstract
     * @access public
     * @param null|string|array $sort
     * @return $this
     */
    abstract public function sort($sort = null);
    
    /**
     * Установка позиции и количества возвращаемых записей
     *
     * @abstract 
     * @access public
     * @param mixed $top
     * @return $this
     */
    abstract public function limit($top = null);
    
    /**
     * Экранирование спецсимволов и обрамление кавычками
     *
     * @abstract 
     * @access public
     * @param mixed $value
     * @return string
     */
    abstract public function escape($value);

    /**
     * Получение значения AUTOINCREMENT поля после выполненного INSERT запроса
     *
     * @abstract 
     * @access public
     * @return integer
     */
    abstract public function lastInsertId();
    
    /**
     * Возвращает количество строк, затронутых последним выполненным запросом
     * INSERT, UPDATE, DELETE
     * 
     * @abstract
     * @access public
     * @return integer
     */
    abstract public function affected();
    
    /**
     * Получение количества выбранных строк в результате выполнения запроса,
     * либо добавляет COUNT() внутрь SELECT запроса
     *
     * @abstract 
     * @access public
     * @param null|string|array $field
     * @return integer
     */
    abstract public function count($field = null); 
    
    /**
     * Возвращает следующую запись из результатов запроса в виде
     * обычного индексного массива
     * 
     * @access public
     * @return array
     */
    abstract public function asRow();
    
    /**
     * Возвращает следующую запись из результатов запроса в виде
     * ассоциативного массива
     *
     * @abstract 
     * @access public
     * @return array
     */
    abstract public function asAssoc();

    /**
     * Возвращает следующую запись из результатов запроса в виде объекта
     * указанного класса
     *
     * @abstract 
     * @access public
     * @param string $class
     * @return object
     */
    abstract public function asObject($class = '\gear\library\GModel');

    /**
     * Возвращает массив всех записей найденных в результате исполнения 
     * запроса
     * 
     * @abstract
     * @access public
     * @return array
     */
    abstract public function asAll();
    
    /**
     * Возвращает текущую запись из результатов запроса в виде ассоциативного
     * массива asAssoc()
     * 
     * @access public
     * @return array
     */
    public function current() { return $this->_current; }
    
    /**
     * Возвращает следующую запись из результатов запроса в виде ассоциативного
     * массива asAssoc()
     * 
     * @access public
     * @return array
     */
    public function next() { return $this->_current = $this->asAssoc(); }
    
    /**
     * Возвращает 0
     * 
     * @access public
     * @return integer
     */
    public function key() { return 0; }
    
    /**
     * Возвращает true, если текущий элемент является валидным, иначе false
     * 
     * @access public
     * @return boolean
     */
    public function valid() { return $this->current() ? true : false; }
    
    /**
     * Возвращает ресурс соединения с сервером базы данных
     * 
     * @access public
     * @return mixed
     */
    public function getHandler() { return $this->owner->getHandler(); }
    
    /**
     * Возвращает коллекцию, для которой создан курсор
     * 
     * @access public
     * @return \gear\library\db\GDbCollection
     */
    public function getCollection() { return $this->owner; }
    
    /**
     * Возвращает базу данных, в которой находится коллекция курсора
     * 
     * @access public
     * @return \gear\library\db\GDbDatabase
     */
    public function getDatabase() { return $this->owner->getDatabase(); }
    
    /**
     * Возвращает соединение с сервером базы данных
     * 
     * @access public
     * @return \gear\library\db\GDbConnection
     */
    public function getConnection() { return $this->owner->getConnection(); }
}
