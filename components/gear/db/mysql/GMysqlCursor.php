<?php

namespace gear\components\gear\db\mysql;
use \gear\Core;
use \gear\library\db\GDbCursor;
use \gear\library\GException;

/** 
 * Курсор
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 11.06.2013
 */
class GMysqlCursor extends GDbCursor
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_current = null;
    /**
     * Логические операции
     */
    protected $_eq = array('$lt' => '<', '$gt' => '>', '$ne' => '<>', '$lte' => '<=', '$gte' => '=>');
    protected $_logic = array('$or' => 'OR', '$and' => 'AND');

    protected $_hasCount = false;
    protected $_hasExplain = false;
    protected $_select = null;
    protected $_fields = array();
    protected $_joins = array();
    protected $_where = array();
    protected $_group = array();
    protected $_sort = array();
    protected $_limit = null;
    /* Public */
    
    /**
     * Возвращает SQL-запрос
     * 
     * @access public
     * @return string
     */
    public function __toString()
    {
        $query = $this->_query ? $this->_query : $this->buildQuery();
        return $query;
    }
    
    /**
     * Построение запроса
     * 
     * @access public
     * @see \gear\library\db\GDbCursor::buildQuery()
     * @return string
     */
    public function buildQuery()
    {
        $this->event('onBeforeBuild');
        $this->_query = ($this->_hasExplain ? 'EXPLAIN ' : '') 
                      . 'SELECT ' . ($this->_hasCount ? $this->_hasCount : (count($this->_fields) ? implode(', ', $this->_fields) : '*')) . ' ' 
                      . 'FROM `' . ($this->_select ? $this->_select : $this->getCollection()->name) . '` ';
        if (count($this->_joins))
            $this->_query .= implode(' ', $this->_joins) . ' ';
        if (count($this->_where))
            $this->_query .= 'WHERE ' . implode(' ', $this->_where) . ' ';
        if (count($this->_sort))
            $this->_query .= 'ORDER BY ' . implode(', ', $this->_sort) . ' ';
        if (!empty($this->_limit))
            $this->_query .= 'LIMIT ' . $this->_limit;
        $this->event('onAfterBuild');
        return $this->_query;
    }
    
    /**
     * Выполнение указанного запроса
     * 
     * @access public
     * @param string $query
     * @return $this
     */
    public function runQuery($query)
    {
        $this->_resource = mysqli_query($this->getHandler(), $query);
        if (!$this->_resource)
            $this->e($this->error(), array('query' => $query));
        return $this;
    }
    
    /**
     * Возвращает текст ошибки
     * 
     * @access public
     * @return string
     */
    public function error()
    {
        return mysqli_error($this->getHandler());
    }

    /**
     * Формирует поисковый запроса поиска элементов по указанному
     * критерию
     * 
     * @access public
     * @param null|array $criteria
     * @param null|string|array $fields
     * @return $this
     */
    public function find($criteria = null, $fields = null)
    {
        $this->event('onBeforeFind');
        $this->_select = $this->_owner->name;
        if (!$fields)
            $this->_fields[] = $this->_select . '.*';
        else
        {
            if (is_string($fields))
                $this->_fields[] = $fields;
            else
            if (is_array($fields))
            {
                foreach($fields as $name => $alias)
                {
                    if (is_numeric($name))
                        $this->_fields[] = strpos($name, '.') ? $name : $this->_select . '.`' . $alias . '`';
                    else
                        $this->_fields[] = (strpos($name, '.') ? $name : $this->_select . '.' . $name) . ' AS `' . $alias . '`';
                }
            }
        }
        $this->where($criteria);
        return $this;
    }

    /**
     * Добавление в коллекцию новой записи
     * Возвращает количество затронутых строк
     * В случае совпадения PRIMARY KEY генерируется исключение
     * 
     * @access public
     * @param mixed $properties
     * @return integer
     */
    public function insert($properties)
    {
        $set = array();
        if ($properties instanceof gear\library\GObject)
            $properties = $properties->props();
        if (is_array($test = reset($properties)))
        {
            $tmp = array();
            $fields = $this->_getFields($test);
            foreach($properties as $record)
            {
                foreach($record as $value)
                    $tmp[] = $this->_escapeValue($value);
                $set[] = implode(', ', $tmp);
            }
            unset($tmp);
        }
        else
        {
            $fields = $this->_getFields($properties);
            foreach($properties as $value)
                $set[] = $this->_escapeValue($value);
            $set = array(implode(', ', $set));
        }
        $this->_query = 'INSERT INTO `' . $this->getCollection()->name . '` ' 
                      . '(' . implode(', ', $fields) . ') VALUES ' 
                      . '(' . implode('), (', $set) . ')';
        return $this->query()->affected();
    }
    
    /**
     * Получение списка полей
     * 
     * @access public
     * @param array $record
     * @return array
     */
    protected function _getFields(array $record)
    {
        $fields = array();
        foreach($record as $field => $value)
            $fields[] = $this->_escapeOperand($field);
        return $fields;
    }
    
    /**
     * Добавление в коллекцию новой записи. В случае совпадения
     * PRIMARY KEY происходит обновление указанных в $updates полей
     * записи
     * Возвращает колчество затронутых полей
     *
     * @access public 
     * @param array|object $properties
     * @param null|array of name properties $updates
     * @return integer
     */
    public function save($properties, $updates = null)
    {
        $set = array();
        $fields = array();
        $updates = array();
        if ($properties instanceof gear\library\GObject)
            $properties = $properties->props();
        foreach($properties as $name => $value)
        {
            $name = $this->_escapeOperand($name);
            $value = $this->_escapeValue($value);
            $updates[] = $name . '=' . $value;
            $fields[] = $name;
            $set[] = $value;
        }
        $this->_query = 'INSERT INTO `' . $this->getCollection()->name . '` (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $set) . ') ';
        if ($updates)
        {
            $updates = array();
            foreach($updates as $name)
            {
                $name = $this->_escapeOperand($name);
                $value = $this->_escapeValue($properties[$name]);
                $updates[] = $name . '=' . $value;
            }
        }
        $this->_query .= 'ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);
        return $this->query()->affected();
    }
    
    /**
     * Обновление указанных полей для записей, соответствующих критерию
     * 
     * @access public
     * @param null|array $criteria
     * @param array|object $properties
     * @return integer
     */
    public function update($criteria = null, $properties)
    {
        if ($properties instanceof gear\library\GObject)
            $properties = $properties->props();
        $set = array();
        foreach($properties as $name => $value)
            $set[] = $this->_escapeOperand($name) . ' = ' . $this->_escapeValue($value);
        $this->_query = 'UPDATE `' . $this->getCollection()->name . '` SET '
                      . implode(', ', $set);
        if ($criteria)
            $this->_query .= ' WHERE ' . $this->_buildCondition($criteria);
        return $this->query()->affected();
    }
    
    /**
     * Удаление записей, соответствующих критерию, либо найденных
     * в результате последнего выполненного SELECT-запроса
     * 
     * @access public
     * @param null|array $criteria
     * @return integer
     */
    public function remove($criteria = null)
    {
        $this->_query = 'DELETE FROM `' . $this->getCollection()->name . '` ';
        if ($criteria)
            $this->_query .= ' WHERE ' . $this->_buildCondition($criteria);
        return $this->query()->affected();
    }
    
    /**
     * Подключение таблицы
     * 
     * @access public
     * @param string|object $collection
     * @param null|array $criterial
     * @return $this
     */
    public function join($collection, $criterial = null)
    {
        $this->_joins[] = 'JOIN ' . (is_object($collection) ? $collection->name : $collection) . ' ON ' . $this->_buildCondition($criterial);
        return $this;
    }

    /**
     * Левое подключение таблицы
     * 
     * @access public
     * @param string|object $collection
     * @param null|array $criterial
     * @return $this
     */
    public function left($collection, $criterial = null)
    {
        $this->_joins[] = 'LEFT JOIN ' . (is_object($collection) ? $collection->name : $collection) . ' ON ' . $this->_buildCondition($criterial);
        return $this;
    }

    /**
     * Правое подключение таблицы
     * 
     * @access public
     * @param string|object $collection
     * @param null|array $criterial
     * @return $this
     */
    public function right($collection, $criterial = null)
    {
        $this->_joins[] = 'RIGHT JOIN ' . (is_object($collection) ? $collection->name : $collection) . ' ON ' . $this->_buildCondition($criterial);
        return $this;
    }

    /**
     * Установка критерия поиска
     * 
     * @access public
     * @param null|array $criteria
     * @return $this
     */
    public function where($criteria = null)
    {
        $this->_where = array();
        if ($criteria)
            $this->_where[] = $this->_buildCondition($criteria);
        return $this;
    }

    /**
     * Построение условного выражения согласно полученного критерия
     * 
     * @access protected
     * @param array $criteria
     * @param null|string $logic
     * @param null|string $col
     * @param null|string $eq
     * @return string
     */
    protected function _buildCondition($criteria, $logic = 'AND', $col = null, $eq = '=')
    {
        $condition = array();
        foreach($criteria as $left => $right)
        {
            if (is_integer($left))
            {
                if (count($condition))
                    $condition[] = $logic;
                if (is_array($right))
                    $condition[] = '(' . $this->_buildCondition($right, $logic) . ')';
                else
                    $condition[] = $right;
            }
            else
            {
                if (isset($this->_logic[$left]))
                {
                    if (is_array($right) && count($right) == 1)
                        $condition[] = $this->_logic[$left];
                    else
                    if (count($condition))
                        $condition[] = $logic;
                    $condition[] = $this->_buildCondition($right, $this->_logic[$left]);
                }
                else
                if (isset($this->_eq[$left]))
                {
                    if (count($condition))
                        $condition[] = $logic;
                    $condition[] = $this->_escapeOperand($col) . ' ' . $this->_eq[$left] . ' ' . $this->_escapeValue($right);
                }
                else
                if ($left === '$not')
                {
                    if (count($condition))
                        $condition[] = $logic;
                    $condition[] = $this->_escapeOperand($col) . ' NOT ' . (is_array($right) ? $this->_buildCondition($right, 'NOT') : $this->_escapeValue($right));
                }
                else
                if ($left === '$in' || $left === '$nin')
                {
                    if (count($condition))
                        $condition[] = $logic;
                    $values = array();
                    foreach($right as $value)
                        $values[] = $this->_escapeValue($value);
                    $condition[] = $this->_escapeOperand($col) . ' ' . ($left === '$in' ? 'IN' : 'NOT IN') . ' (' . implode(', ', $values) . ')';
                }
                else
                if ($left === '$fn')
                {
                    if (count($condition))
                        $condition[] = $logic;
                    if (is_string($right))
                        $condition[] = $this->_escapeOperand($col) . ' ' . $eq . ' ' . $right;
                    else
                    {
                        $fnName = $right[0];
                        unset($right[0]);
                        $values = array();
                        foreach($right as $value)
                            $values[] = $this->_escapeValue($value);
                        $condition[] = $this->_escapeOperand($col) . ' ' . $eq . ' ' . $fnName . '(' . implode(', ', $values) . ')';
                    }
                }
                else
                {
                    if (count($condition))
                        $condition[] = $logic;
                    $condition[] = is_array($right)
                                   ? $this->_buildCondition($right, $logic, $left, $eq)
                                   : $this->_escapeOperand($left) . ' ' . $eq . ' ' . $this->_escapeValue($right);
                }
            }
        }
        return implode(' ', $condition);
    }
    
    /**
     * Экранирование спецсимволов и обрамление кавычками
     * 
     * @access public
     * @param mixed $value
     * @return string
     */
    public function escape($value)
    {
        return "'" . mysqli_real_escape_string($this->getHandler(), $value) . "'";
    }
    
    /**
     * Обработка левого операнда
     * 
     * @access protected
     * @param string $value
     * @return string
     */
    protected function _escapeOperand($value)
    {
        if ($value[0] === ':')
            return strpos($value, '.') ? substr($value, 1) : $this->getOwner()->name . '.' . substr($value, 1);
        else
        if (preg_match('/^[A-Z_]+\(.*\)$/', $value))
            return $value;
        else
            return strpos($value, '.') ? $value : $this->getOwner()->name . '.' . $value;
    }
    
    /**
     * Обработка правого операнда
     * 
     * @access protected
     * @param string $value
     * @return string
     */
    protected function _escapeValue($value)
    {
        if (strlen($value) && $value[0] === ':')
            return strpos($value, '.') ? substr($value, 1) : $this->getOwner()->name . '.' . substr($value, 1);
        else
        if (preg_match('/^[A-Z_]+\(.*\)$/', $value))
            return $value;
        else
        if (is_null($value))
            return 'NULL';
        else
            return $this->escape($value);
    }

    /**
     * Установка группировки результатов запроса
     * 
     * @access public
     * @param null|string|array $group
     * @return $this
     */
    public function group($group = null)
    {
        $this->event('onBeforeCommand', new \gear\library\GEvent($this), 'group');
        if (is_string($group))
            $this->_group[] = $group;
        else
        if (is_array($group))
        {
            foreach($group as $field => $order)
            {
                if (is_numeric($field))
                    $this->_group[] = strpos($order, '.') ? $order : $this->_select . '.`' . $order . '`';
                else
                    $this->_group[] = (strpos($field, '.') ? $field : $this->_select . '.`' . $field . '`') 
                                    . ' ' . ((int)$order === 1 ? 'ASC' : 'DESC');
            }
        }
        return $this;
    }
    
    /**
     * Установка сортировки результатов запроса
     * 
     * @access public
     * @param null|string|array $sort
     * @return $this
     */
    public function sort($sort = null)
    {
        $this->event('onBeforeCommand', new \gear\library\GEvent($this), 'sort');
        if (is_string($sort))
            $this->_sort[] = $sort;
        else
        if (is_array($sort))
        {
            foreach($sort as $field => $order)
            {
                $field = strpos($field, '.') ? $field : $this->_select . '.`' . $field . '`';
                if (is_numeric($order))
                    $order = $field . ' ' . ((int)$order === 1 ? 'ASC' : 'DESC');
                else
                if (is_array($order))
                {
                    foreach($order as &$value)
                        $value = $this->escape($value);
                    unset($value);
                    $order = 'FIELD(' . $field . ', ' . implode(', ', $order) . ')';
                }
                $this->_sort[] = $order;
            }
        }
        return $this;
    }
    
    /**
     * Установка позиции и количества возвращаемых записей
     * 
     * @access public
     * @param mixed $top
     * @return $this
     */
    public function limit($top = null)
    {
        $this->event('onBeforeCommand', new \gear\library\GEvent($this), 'limit');
        if (!func_num_args())
            $this->_limit = null;
        else
        if (func_num_args() == 1)
            $this->_limit = is_array($top) ? implode(', ', $top) : (!$top ? null : $top);
        else
        if (func_num_args() == 2)
            $this->_limit = implode(', ', func_get_args());
        return $this;
    }
    
    /**
     * Получение значения AUTOINCREMENT поля после выполненного INSERT запроса
     * 
     * @access public
     * @return integer
     */
    public function lastInsertId()
    {
        if (!$this->_lastInsertId)
        {
            if (!$this->_resource)
                $this->query();
            $this->_lastInsertId = mysqli_insert_id($this->getHandler());
        }
        return $this->_lastInsertId;
    }
    
    /**
     * Возвращает количество строк, затронутых последним выполненным запросом
     * INSERT, UPDATE, DELETE
     * 
     * @abstract
     * @access public
     * @return integer
     */
    public function affected()
    {
        if (!$this->_resource)
            $this->query();
        return mysqli_affected_rows($this->getHandler());
    }

    /**
     * Получение количества выбранных строк в результате выполнения запроса,
     * либо добавляет COUNT() внутрь SELECT запроса
     * 
     * @access public
     * @param null|string|array $field
     * @return integer
     */
    public function count($field = null)
    {
        if ($field === null)
        {
            if (!$this->_resource)
                $this->query();
            return mysqli_num_rows($this->_resource);
        }
        else
        {
            $this->event('onBeforeCommand', new \gear\library\GEvent($this), 'count');
            if (is_array($field))
                list($field, $alias) = each($field);
            $this->_count = 'COUNT(' . ($field ? $this->_escapeOperand($field) : '*') . ')' . (isset($alias) ? ' AS `' . $alias . '`' : '');
            $count = $this->query()->asAssoc();
            $this->_count = null;
            return $count;
        }
    }
    
    /**
     * Возвращает информацию о выполняемом запросе в виде массива
     * 
     * @access public
     * @return array
     */
    public function explain()
    {
        if (!$this->_query)
            $this->_buildQuery();
        return preg_match('/^SELECT/', $this->_query) ? $this->runQuery('EXPLAIN ' . $this->_query)->asAll() : null;
    }
    
    /**
     * Возвращает следующую запись из результатов запроса в виде
     * обычного индексного массива
     * 
     * @access public
     * @return array
     */
    public function asRow()
    {
        if (!$this->_resource)
            $this->query();
        return mysqli_fetch_row($this->_resource);
    }
    
    /**
     * Возвращает следующую запись из результатов запроса в виде
     * ассоциативного массива
     * 
     * @access public
     * @return array
     */
    public function asAssoc()
    {
        if (!$this->_resource)
            $this->query();
        return mysqli_fetch_assoc($this->_resource);
    }
    
    /**
     * Возвращает следующую запись из результатов запроса в виде объекта
     * указанного класса
     * 
     * @access public
     * @param string $class
     * @return object
     */
    public function asObject($class = '\\gear\\library\\GModel')
    {
        if (!$this->_resource)
            $this->query();
        $properties = mysqli_fetch_assoc($this->_resource);
        $object = false;
        if ($properties)
            $object = is_array($class) && is_callable($class) ? call_user_func($class, $properties) : new $class($properties);
        return $object;
    }
    
    /**
     * Возвращает массив всех записей найденных в результате исполнения 
     * запроса. Каждый элемент массива является ассоциативным массивом
     * получченным из mysqli_fetch_assoc()
     * 
     * @access public
     * @return array
     */
    public function asAll()
    {
        if (!is_object($this->_resource)) 
            $this->query();
        mysqli_data_seek($this->_resource, 0);
        $items = array();
        while($row = mysqli_fetch_assoc($this->_resource))
           $items[] = $row;
        return $items;
    }
    
    /**
     * Перемотка в начало списка результата
     * 
     * @access public
     * @return array
     */
    public function rewind()
    {
        $this->_resource ? mysqli_data_seek($this->_resource, 0) : $this->query();
        return $this->_current = $this->asAssoc();
    }

    public function reset()
    {
        $this->_count = false;
        $this->_select = null;
        $this->_fields = array();
        $this->_joins = array();
        $this->_where = array();
        $this->_group = array();
        $this->_sort = array();
        $this->_limit = null;
        $this->_query = null;
        $this->_resource = null;
    }
    
    public function onBeforeFind()
    {
        $this->reset();
        return true;
    }
    
    public function onBeforeCommand($event, $command = null)
    {
        $this->_query = null;
        $this->_resource = null;
        $this->_items = array();
        if (!$this->_select)
            $this->_select = $this->_owner->name;
        if ($command === 'group')
            $this->_group = array();
        else
        if ($command === 'sort')
            $this->_sort = array();
    }
}

/** 
 * Класс исключений курсора
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 11.06.2013
 */
class MysqlCursorException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
