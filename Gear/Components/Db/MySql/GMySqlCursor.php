<?php

namespace Gear\Components\Db\Mysql;

use Gear\Core;
use Gear\Interfaces\DbCursorInterface;
use Gear\Interfaces\ModelInterface;
use Gear\Interfaces\ObjectInterface;
use Gear\Library\Db\GDbCursor;
use Psr\Log\LogLevel;


/**
 * Класс для работы с mysql-запросами
 *
 * @package Gear Framework
 *
 * @property GMySqlCollection collection
 * @property GMySqlConnectionComponent connection
 * @property GMySqlDatabase database
 * @property \mysqli handler
 * @property int lastInsertId
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
class GMySqlCursor extends GDbCursor implements DbCursorInterface
{
    /* Traits */
    /* Const */
    /* Private */
    private $_logics = ['$or' => 'OR', '$and' => 'AND'];
    private $_eqs = ['$lt' => '<', '$gt' => '>', '$ne' => '<>', '$lte' => '<=', '$gte' => '>='];
    private $_sfuncs = ['$isn' => 'IS NULL', '$isnn' => 'IS NOT NULL'];
    private $_funcs = ['$like' => 'LIKE', '$nlike' => 'NOT LIKE', '$rlike' => 'RLIKE', '$nrlike' => 'NOT RLIKE', '$regx' => 'REGEXP', '$nregx' => 'NOT REGEXP'];
    /* Protected */
    protected $_current = false;
    protected $_queryBuild = [];
    protected $_factoryQueryBuild = [
        'class' => '\Gear\Library\GModel',
        'fields' => [],
        'from' => null,
        'join' => [],
        'where' => [],
        'group' => [],
        'having' => [],
        'order' => [],
        'limit' => null,
    ];
    /* Public */

    /**
     * Возвращает количество строк, затронутых последним выполненным запросом
     * INSERT, UPDATE, DELETE
     *
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public function affected(): int
    {
        if (!$this->result) {
            $this->runQuery($this->buildQuery());
        }
        return $this->handler->affected_rows;
    }

    /**
     * Выполняет запрос и возвращает массив всех найденных записей
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function all(): array
    {
        if (!$this->result) {
            $this->query();
        }
        return $this->result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Выполняет запрос и возвращает ассоциативный массив найденной записи
     *
     * @return array|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function asAssoc(): ?iterable
    {
        if (!$this->result) {
            $this->query();
        }
        return $this->result->fetch_assoc();
    }

    /**
     * Выполняет запрос и возвращает объект найденной записи, реализующий интерфейс IModel
     *
     * @param string $class
     * @return ModelInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function asObject(string $class = '\Gear\Library\GModel'): ?ModelInterface
    {
        if (!$this->result) {
            $this->query();
        }
        return ($properties = $this->result->fetch_assoc()) ? $this->factory($properties, $this->collection) : null;
    }

    /**
     * Выполняет запрос и возвращает индексный массив найденной записи
     *
     * @return array|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function asRow(): ?iterable
    {
        if (!$this->result) {
            $this->query();
        }
        return $this->result->fetch_array(MYSQLI_NUM);
    }

    /**
     * Создание sql-запроса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function buildQuery(): string
    {
        Core::syslog(LogLevel::INFO, 'Build MySQL query ', ['__func__' => __METHOD__, '__line__' => __LINE__], true);
        $this->_query = 'SELECT SQL_CALC_FOUND_ROWS ';
        $fields = $this->_queryBuild->fields;
        if ($fields) {
            $this->_query .= (is_array($fields) ? implode(', ', $fields) : $fields);
        } else {
            $this->_query .= "*";
        }
        $this->_query .= ' FROM ' . $this->_queryBuild->from;
        $join = $this->_queryBuild->join;
        if ($join) {
            $this->_query .= ' ' . (is_array($join) ? implode(' ', $join) : $join);
        }
        $where = $this->_queryBuild->where;
        if ($where) {
            $this->_query .= ' WHERE ' . (is_array($where) ? implode(' AND ', $where) : $where);
        }
        $group = $this->_queryBuild->group;
        if ($group) {
            $this->_query .= ' GROUP BY ' . (is_array($group) ? implode(', ', $group) : $group);
        }
        $having = $this->_queryBuild->having;
        if ($having) {
            $this->_query .= ' HAVING ' . (is_array($having) ? implode(' AND ', $having) : $having);
        }
        $order = $this->_queryBuild->order;
        if ($order) {
            $this->_query .= ' ORDER BY ' . (is_array($order) ? implode(', ', $order) : $order);
        }
        $limit = $this->_queryBuild->limit;
        if ($limit) {
            $this->_query .= ' LIMIT ' . (is_array($limit) ? implode(', ', $limit) : $limit);
        }
        return $this->_query;
    }

    /**
     * Получение количества выбранных строк в результате выполнения запроса,
     * либо добавляет COUNT() внутрь SELECT запроса
     *
     * @param string $field
     * @return integer|GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function count(string $field = '')
    {
        if ($field) {
            $this->_queryBuild->fields[] = "COUNT($field)";
            $count = $this;
        } else {
            if (!$this->result) {
                $this->query();
                $result = $this->result;
                $this->runQuery('SELECT FOUND_ROWS() AS countFoundRows');
                $count = $this->asAssoc()['countFoundRows'];
                $this->result = $result;
            } else {
                $count = $this->result->num_rows;
            }
        }
        return $count;
    }

    /**
     * Удаление записей соответствующих критерию
     *
     * @param array|ModelInterface $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function delete($criteria = []): DbCursorInterface
    {
        if (!$criteria) {

        } else if (is_array($criteria)) {
            $criteria = $this->_prepareCriteria($criteria);
            $query = 'DELETE FROM `' . $this->getCollectionName() . "` WHERE " . $criteria;
        } else if ($criteria instanceof ModelInterface) {
            $pk = $criteria->primaryKeyName;
            $query = 'DELETE FROM `' . $this->getCollectionName() . "` WHERE `$pk` = " . $this->_prepareValue('"' . $criteria->$pk . '"');
        } else {
            throw new \InvalidArgumentException('Invalid arguments to delete');
        }
        return $this->runQuery($query);
    }

    /**
     * Экранирование спецсимволов и обрамление кавычками
     *
     * @param string $value
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function escape($value): string
    {
        return $this->handler->real_escape_string($value);
    }

    /**
     * Устанавливает набор полей, возвращаемых в результате запроса
     *
     * $this->fields(['col1', 'col2' => -1, 'col3' => 1, 'col4 as field4', 'MAX(col5)']);
     *
     * @param string|array $fields
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function fields($fields): DbCursorInterface
    {
        $tempFields = $this->_queryBuild->fields;
        if (is_array($fields)) {
            foreach($fields as $name => $entry) {
                if (is_numeric($name)) {
                    $name = $entry;
                    if ($name !== '*' && !strpos($name, '.') && !preg_match('/\s(as)\s/i', $name) &&
                        strpos($name, '(') === false) {
                        $name = "`$name`";
                    }
                    $tempFields[] = $name;
                } else {
                    if (is_numeric($entry) && $entry > 0) {
                        if (!strpos($name, '.') && !preg_match('/\s(as)\s/i', $name) &&
                            strpos($name, '(') === false) {
                            $name = "`$name`";
                        }
                        $tempFields[] = $name;
                    } elseif (!is_numeric($entry)) {
                        if (!strpos($name, '.') && !preg_match('/\s(as)\s/i', $name) &&
                            strpos($name, '(') === false) {
                            $name = "`$name`";
                        }
                        $name .= " AS $entry";
                        $tempFields[] = $name;
                    }
                }
            }
        } else if (is_string($fields)) {
            $tempFields[] = $fields;
        } else {
            throw new \InvalidArgumentException('Invalid arguments to fields');
        }
        $this->_queryBuild->fields = $tempFields;
        return $this;
    }

    /**
     * Поиск записей по указанному критерию
     *
     * @param string|array $criteria
     * @param string|array $fields
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function find($criteria = [], $fields = []): DbCursorInterface
    {
        $this->reset();
        $from = $this->getCollectionName();
        $this->_queryBuild->from = "`$from`" . ($this->collection->alias ? ' AS ' . $this->collection->alias : '');
        return $this->fields($fields)->where($criteria);
    }

    public function from($collection = '', string $alias = ''): DbCursorInterface
    {
        if (!$collection) {
            $this->_queryBuild->from = $this->getCollectionName();
        } elseif ($collection instanceof GMySqlCollection) {
            if ($alias) {
                $collection->alias = $alias;
            }
            $this->_queryBuild->from = $collection->name;
        } else {
            $this->_queryBuild->from = $collection;
        }
        return $this;
    }

    /**
     * Возвращает ID последней вставленной записи
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getLastInsertId(): int
    {
        if (!$this->result) {
            $this->query();
        }
        return $this->handler->insert_id;
    }

    /**
     * Установка группировки результатов запроса
     *
     * @param string|array $group
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function group($group = ''): DbCursorInterface
    {
        $tempGroup = $this->_queryBuild->group;
        if (is_array($group)) {
            foreach($group as $name => &$order) {
                if (!is_numeric($name)) {
                    $order = "$order " . ($order === self::ASC ? 'ASC' : 'DESC');
                }
            }
            unset($order);
            $tempGroup = array_merge($tempGroup, $group);
        } else if (is_string($group)) {
            $tempGroup[] = $group;
        } else {
            throw new \InvalidArgumentException('Invalid arguments to groupping');
        }
        $this->_queryBuild->group = $tempGroup;
        return $this;
    }

    /**
     * Дополнительная выборка HAVING
     *
     * @param array $criteria
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function having($criteria = []): GDbCursor
    {
        $having = $this->_queryBuild->having;
        $criteria = $this->_prepareCriteria($criteria);
        if ($criteria) {
            $having[] = $criteria;
            $this->_queryBuild->having = $having;
        }
        return $this;
    }

    /**
     * Подключение таблицы
     *
     * @param string|object $collection
     * @param array $criteria
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function inner($collection, $criteria = []): GDbCursor
    {
        $this->_join('inner', $collection, $criteria);
        return $this;
    }

    /**
     * Добавление в коллекцию новой записи
     * Возвращает количество затронутых строк
     * В случае совпадения PRIMARY KEY генерируется исключение
     *
     * @param array|object $properties
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function insert($properties): DbCursorInterface
    {
        $this->reset();
        $result = 0;
        if ($properties instanceof ModelInterface) {
            $result = $properties;
            $properties = $result->props();
        } else if (is_object($properties)) {
            $result = $properties;
            $properties = get_object_vars($result);
        } else if (!is_array($properties)) {
            throw new \InvalidArgumentException('Invalid properties to insert');
        }
        list($names, $values) = $this->_prepareInsert($properties);
        $query = "INSERT INTO `" . $this->getCollectionName() . "` $names VALUES $values";
        $this->runQuery($query);
        if (is_object($result) && ($pk = $result->primaryKeyName)) {
            $result->$pk = $this->getLastInsertId();
        }
        return $this;
    }

    /**
     * Подключение таблицы
     *
     * @param string|array|object $collection
     * @param array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function join($collection, $criteria = []): DbCursorInterface
    {
        $this->_join('join', $collection, $criteria);
        return $this;
    }

    /**
     * Левое подключение таблицы
     *
     * @param string|array|object $collection
     * @param array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function left($collection, array $criteria = []): DbCursorInterface
    {
        $this->_join('left', $collection, $criteria);
        return $this;
    }

    /**
     * Установка позиции и количества возвращаемых записей
     *
     * $this->limit(10); // будут возвращены первые 10 найденные записи
     * $this->limit(); // по-умолчани будет установлен лимит равный 1
     * $this->limit(5, 10); // будут возвращены 10 записей начиная с 5-ой
     * $this->limit([5, 10]); // аналогично предыдущему примеру
     *
     * @param array $limit
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function limit(...$limit): DbCursorInterface
    {
        if (!$limit) {
            $this->_queryBuild->limit = [0, 1];
        } else if (count($limit) === 1) {
            $limit = reset($limit);
            is_array($limit) ? $this->limit(...$limit) : $this->_queryBuild->limit = [0, $limit];
        } else if (count($limit) > 1) {
            list($top, $limit) = $limit;
            $this->_queryBuild->limit = [$top, $limit];
        }
        return $this;
    }

    /**
     * Сброс результатов выполнения последнего запроса
     *
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function reset(): DbCursorInterface
    {
        if ($this->result) {
            $this->result->free();
            $this->result = null;
        }
        $this->_query = null;
        $this->_queryBuild = $this->factory($this->_factoryQueryBuild);
        $cursor = $this;
        $this->_queryBuild->builderExecute = function() use ($cursor) {
            $cursor->buildQuery();
        };
        return $this;
    }

    /**
     * Правое подключение таблицы
     *
     * @param string|array|object $collection
     * @param array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function right($collection, array $criteria = []): DbCursorInterface
    {
        $this->_join('right', $collection, $criteria);
        return $this;
    }

    /**
     * Выполнение составленного SQL-запроса
     *
     * @param string $query
     * @param array $params
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function runQuery(string $query, ...$params): DbCursorInterface
    {
        if ($params) {
            $bindParams = [];
            foreach($params as $param) {
                $bindParams[] = $this->eascape($param);
            }
            $query = sprintf($query, ...$bindParams);
        }
        if (defined('DEBUG') && DEBUG === true) {
            Core::syslog(LogLevel::INFO, 'Run MySQL query {query}', ['query' => $query, '__func__' => __METHOD__, '__line__' => __LINE__], true);
        }
        if (!($this->result = $this->handler->query($query))) {
            $handler = $this->handler;
            throw self::DbCursorException('Invalid run query: {errorMessage}', ['query' => $query, 'errorMessage' => $handler->error]);
        }
        return $this;
    }

    /**
     * Добавление в коллекцию новой записи. В случае совпадения
     * PRIMARY KEY происходит обновление указанных в $updates полей
     * записи
     * Возвращает количество затронутых полей
     *
     * @param array|ObjectInterface $properties
     * @param array $updates
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function save($properties, array $updates = []): DbCursorInterface
    {
        $this->reset();
        $result = 0;
        if ($properties instanceof ObjectInterface) {
            $result = $properties;
            $properties = $result->props();
        } elseif (is_object($properties)) {
            $result = $properties;
            $properties = get_object_vars($result);
        } elseif (!is_array($properties)) {
            throw new \InvalidArgumentException('Invalid properties to insert');
        }
        list($names, $values) = $this->_prepareInsert($properties);
        $query = "INSERT INTO `" . $this->getCollectionName() . "` $names VALUES $values";
        if (!$updates && is_object($result)) {
            $pk = $result->primaryKeyName;
            $props = $result instanceof IObject ? $result->props() : get_object_vars($result);
            $properties = [];
            foreach($props as $name => $value) {
                if (!$pk || $name !== $pk) {
                    $properties[] = $name;
                }
            }
            $updates = $this->_prepareUpdate($properties, $result);
        } elseif ($updates) {
            if (is_object($result)) {
                $updates = $this->_prepareUpdate($updates, $result);
            } else if (\Arrays::IsAssoc($updates)) {
                $updates = $this->_prepareUpdate($updates);
            } else {
                throw new \InvalidArgumentException('Invalid argument <updates> to save');
            }
        } else {
            throw new \InvalidArgumentException('Invalid argument <updates> to save');
        }
        $query .= " ON DUPLICATE KEY UPDATE " . $updates;
        $this->runQuery($query);
        if (is_object($result) && ($pk = $result->primaryKeyName)) {
            if (($id = $this->getLastInsertId())) {
                $result->$pk = $id;
            }
        }
        return $this;
    }

    /**
     * Установка сортировки результатов запроса
     *
     * @param string|array $sort
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function sort($sort = ''): DbCursorInterface
    {
        $tempSort = $this->_queryBuild->order;
        if (is_array($sort)) {
            foreach($sort as $col => &$order) {
                if (!is_numeric($col)) {
                    if (preg_match('/^[a-z0-9_]+\.[a-z0-9_]+$/i', $col)) {
                        $rec = explode('.', $col);
                        $col = '`' . implode('`.`', $rec) . '`';
                        $order = "$col " . ($order === self::ASC ? 'ASC' : 'DESC');
                    } elseif ($col[0] === '$') {
                        $order = strtoupper(substr($col, 1)) . '(' . is_array($order) ? implode(', ', $this->_prepareValue($order)) : $this->_prepareValue($order) . ')';
                    } else {
                        $order = "`$col` " . ($order === self::ASC ? 'ASC' : 'DESC');
                    }
                }
            }
            $tempSort = array_merge($tempSort, $sort);
        } else {
            $tempSort[] = $tempSort;
        }
        $this->_queryBuild->order = $tempSort;
        return $this;
    }

    /**
     * Обновление указанных полей для записей, соответствующих критерию
     *
     * $this->update([], ['a' => 2]);
     * $this->update(['a' => 2], ['a' => 3]);
     * $model = new \gear\library\GModel(['a' => 3]);
     * $this->update($model, ['a' => 4]);
     * $model->a = 5;
     * $this->update($model);
     * $model->a = 6;
     * $this->update($model, ['a']);
     *
     * @param array|object $criteria
     * @param array $properties
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function update($criteria = [], array $properties = []): DbCursorInterface
    {
        $this->reset();
        $result = $criteria;
        if (!is_array($criteria) && !is_string($criteria) && !is_object($criteria)) {
            throw new \InvalidArgumentException('Invalid argument <criteria> to update');
        }
        if (!$properties && !is_object($criteria)) {
            throw new \InvalidArgumentException('Invalid argument <properties> to update');
        }
        $properties = $this->_prepareUpdate($properties, $result);
        $criteria = $this->_prepareCriteria($result);
        $query = 'UPDATE `' . $this->getCollectionName() . '` SET ' . $properties . ($criteria ? ' WHERE ' . $criteria : '');
        $this->runQuery($query);
        return $this;
    }

    /**
     * Формирование критерия поиска
     *
     * @param string|array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function where($criteria = []): DbCursorInterface
    {
        $where = $this->_queryBuild->where;
        $criteria = $this->_prepareCriteria($criteria);
        if ($criteria) {
            $where[] = $criteria;
            $this->_queryBuild->where = $where;
        }
        return $this;
    }

    private function _join($type, $collection, $criteria = [])
    {
        $joinTypes = ['join' => 'JOIN', 'left' => 'LEFT JOIN', 'right' => 'RIGHT JOIN', 'inner' => 'INNER JOIN'];
        $join = $this->_queryBuild->join;
        $criteria = $this->_prepareCriteria($criteria);
        $type = $joinTypes[strtolower($type)];
        $alias = '';
        $joinCollection = $type;
        if (is_array($collection)) {
            $alias = reset($collection);
            $collection = key($collection);
        }
        if (is_object($collection)) {
            $joinCollection .= " `" . $collection->name . '`';
        } else {
            $joinCollection .= " `" . $collection . '`';
        }
        $join[] = "$joinCollection " . ($alias !== '' ? " AS $alias" : '') . " ON $criteria";
        $this->_queryBuild->join = $join;
    }

    /**
     * $this->where(['a' => 2]);
     * $this->where(['$ne' => ['a' => 2]]);
     * $this->where(['a' => 'NOW()']);
     * $this->where(['a' => ':b']);
     * $this->where(['a' => ['&lt' => 2]]);
     * $this->where(['a' => [2, 3, 4]]);
     * $this->where(['a' => ['$lt' => 2, '$or' => 3]]);
     * $this->where(['a' => ['$lt' => 2, '$or' => ['$gt' => 7]]]);
     * $this->where([['a' => 2, '$and' => ['b' => 3]]]);
     *
     * @param int|string|array|ModelInterface $criteria
     * @param string $logic
     * @param null|string $op
     * @param string $eq
     * @return string
     * @since 0.0.1
     * @version 0.0.2
     */
    private function _prepareCriteria($criteria, $logic = 'AND', $op = null, $eq = '='): string
    {
        if ($criteria instanceof ModelInterface) {
            $pk = $criteria->primaryKeyName;
            $result = $this->_prepareCriteria([$pk => '"' . $criteria->$pk . '"']);
        } elseif (is_string($criteria) || is_numeric($criteria)) {
            $result = $criteria;
        } elseif (is_array($criteria)) {
            $result = [];
            foreach ($criteria as $left => $right) {
                if (is_numeric($left)) {
                    if (is_array($right)) {
                        $result[] = ($result ? " $logic " : "") . '(' . $this->_prepareCriteria($right, $logic, $op, $eq) . ')';
                    } else {
                        if ($op) {
                            $result[] = ($result ? " $logic " : "") . "$left $eq " . $this->_prepareValue($right);
                        } else {
                            $result[] = ($result ? " $logic " : "") . $this->_prepareValue($right);
                        }
                    }
                } elseif (in_array($left, array_keys($this->_logics))) { // AND, OR
                    /**
                     * SQL: Using AND, OR
                     */
                    $result[] = ($result ? $this->_logics[$left] : '') . ' ' . $this->_prepareCriteria($right, $this->_logics[$left], $op, $eq);
                } elseif (in_array($left, array_keys($this->_eqs))) { // <, >, <=, =>, <>
                    /**
                     * SQL: Using operations: <, >, <>, <=, =>
                     */
                    if (!$op) {
                        $result[] = ($result ? " $logic " : "") . $this->_prepareCriteria($right, $logic, null, $this->_eqs[$left]); // ['$lt' => ['a' => 3]]
                    } else {
                        $result[] = ($result ? " $logic " : "") . " $op " . $this->_eqs[$left] . " " . $this->_prepareCriteria($right, $logic); // ['a' => ['$lt' => 3]]
                    }
                } elseif ($left === '$in') {
                    /**
                     * SQL: COL IN (VAL1, VAL2, ... VALn)
                     */
                    $result[] = ($result ? $logic : "") . " $op IN (" . implode(', ', $this->_prepareValue($right)) . ')';
                } elseif ($left === '$nin') {
                    /**
                     * SQL: COL NOT IN (VAL1, VAL2, ... VALn)
                     */
                    $result[] = ($result ? $logic : "") . " $op NOT IN (" . implode(', ', $this->_prepareValue($right)) . ')';
                } elseif (in_array($left, array_keys($this->_sfuncs))) {
                    /**
                     * SQL: Using IS NULL, IS NOT NULL
                     */
                    $result[] = ($result ? $logic : "") . $this->_prepareOperand($right, true) . " " . $this->_sfuncs[$left];
                } elseif (in_array($left, array_keys($this->_funcs))) {
                    /**
                     * SQL: Using LIKE, NOT LIKE, RLIKE, NOT RLIKE, REGEXP, NOT REGEXP
                     */
                    if (!$op) { // ['$like' => ['a' => 'pattern']]
                        list($operand, $pattern) = $right;
                        $result[] = ($result ? $logic : "") . $this->_prepareOperand($result, true) . " \"$pattern\"";
                    } else { // ['a' => ['$like' => 'pattern']]
                        $result[] = ($result ? $logic : "") . " $op " . $this->_funcs[$left] . " " . $this->_prepareValue($right);
                    }
                } elseif ($left === '$bw') {
                    /**
                     * SQL: COL BETWEEN VAL1 AND VAL2
                     */
                    if (!$op) {
                        // ['$bw' => [':a' => [1, 10]]]
                        $operand = $this->_prepareOperand(key($right), true);
                        $values = $this->_prepareValue(current($right));
                        $result[] = ($result ? $logic : "") . " $operand BETWEEN " . implode(' AND ', $values);
                    } else {
                        // [':a' => ['$bw' => [1 => 10]]]
                        $right = $this->_prepareValue($right);
                        $result[] = ($result ? $logic : "") . " $op BETWEEN " . implode(' AND ', $right);
                    }
                } elseif ($left[0] === '$') {
                    /**
                     * SQL: Using functions MAX(), MIN(), DATE(), NOW() and etc.
                     *
                     * ['$MAX' => ':a']
                     * ['$DATE_SUB' => ['NOW()', 'INTERVAL 1 DAYS']]
                     */
                    $left = substr($left, 1);
                    $right = $this->_prepareValue($right);
                    if (is_array($right)) {
                        $right = implode(', ', $right);
                    }
                    $result[] = ($result ? $logic : "") . " " . ($op ? " $op " . ($eq ? $eq : '') : '') . " $left($right)";
                } else {
                    $left = $this->_prepareOperand($left, true);
                    if (is_array($right)) {
                        if (!\Arrays::isAssoc($right)) {
                            $result[] = ($result ? $logic : "") . " $left IN (" . implode(', ', $this->_prepareValue($right)) . ')';
                        } else {
                            $result[] = ($result ? $logic : "") . " " . $this->_prepareCriteria($right, $logic, $left, $eq);
                        }
                    } else {
                        $right = $this->_prepareValue($right);
                        $result[] = ($result ? $logic : "") . " $left " . ($right === 'NULL' ? 'IS' : $eq) . " $right";
                    }
                }
            }
            $result = trim(implode(" ", $result));
        } else {
            throw new \InvalidArgumentException('Invalid arguments criteria to find');
        }
        return $result;
    }

    /**
     * Возвращает массив подготовленных полей и данных для вставки
     *
     * @param array $properties
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    private function _prepareInsert(array $properties): array
    {
        /**
         * \Arrays это алиас класса \gear\helpers\HArray
         */
        if (\Arrays::isAssoc($properties)) {
            $names = array_keys($properties);
            foreach($properties as &$value) {
                $value = '"' . $this->escape($value) . '"';
            }
            unset($value);
            $properties = '(' . implode(', ', $properties) . ')';
        } else {
            $first = reset($properties);
            if (is_object($first)) {
                $names = array_keys($first instanceof ModelInterface ? $first->props() : get_object_vars($first));
            } else {
                $names = array_keys($first);
            }
            foreach ($properties as $index => $p) {
                if (is_object($p)) {
                    $p = $p instanceof ModelInterface ? $p->props() : get_object_vars($p);
                }
                foreach ($p as &$value) {
                    $value = '"' . $this->escape($value) . '"';
                }
                unset($value);
                $properties[$index] = '(' . implode(', ', $p) . ')';
            }
            $properties = implode(', ', $properties);
        }
        $names = '(`' . implode('`, `', $names) . '`)';
        return [$names, $properties];
    }

    private function _prepareOperand($operand, $strictLeft = false)
    {
        if (!is_numeric($operand)) {
            if ($operand === null || preg_match('/^null$/i', $operand)) {
                $operand = 'NULL';
            } elseif (preg_match('/^[a-z0-9_]+\.[a-z0-9_]+$/i', $operand)) {
                $rec = explode('.', $operand);
                $operand = '`' . implode('`.`', $rec) . '`';
            } elseif (strpos($operand, ' AS ') !== false) {
                $operand = preg_replace('/\s{2,}/', ' ', $operand);
                list($left, $alias) = explode(' AS ', $operand);
                $operand = (preg_match('/^[A-Z0-9_]+$/i', $left) ? "`$left`" : $left) . " AS `$alias`";
            } elseif ($operand[0] === ':') { // Column in table $operand == ':id'
                $operand = '`' . substr($operand, 1) . '`';
            } elseif (preg_match('/^\$[A-Z0-9_]+\(.*\)$/i', $operand)) { // Function $operand == '$SUM(price)'
                $operand = substr($operand, 1);
            } elseif (!preg_match('/^[a-z0-9_]+\(.*\)$/i', $operand)) {
                $operand = $strictLeft ? "`$operand`" : false;
            }
        } else {
            $operand = false;
        }
        return $operand;
    }

    /**
     * Возвращает подготовленную к обновлению строку, как часть sql-запроса
     *
     * @param $properties
     * @param null $source
     * @return string
     * @since 0.0.1
     * @version 0.0.2
     */
    private function _prepareUpdate($properties, $source = null): string
    {
        $result = [];
        if ($source && is_object($source)) {
            if (!$properties) {
                $properties = array_keys($source instanceof ModelInterface ? $source->props() : get_class_vars(get_class($source)));
            }
            $pk = $source->primaryKeyName;
            if (\Arrays::IsAssoc($properties)) {
                foreach ($properties as $name => $value) {
                    if (!$pk || $pk !== $name) {
                        $source->$name = $value;
                        $result[] = "`$name` = '" . $this->escape($source->$name) . "'";
                    }
                }
            } else {
                foreach ($properties as $name) {
                    if (!$pk || $pk !== $name) {
                        $result[] = "`$name` = '" . $this->escape($source->$name) . "'";
                    }
                }
            }
        } else {
            foreach ($properties as $name => $value) {
                $result[] = "`$name` = " . $this->_prepareValue($value);
            }
        }
        return implode(', ', $result);
    }

    private function _prepareValue($value)
    {
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = $this->_prepareValue($val);
            }
            unset($val);
        } elseif ($value === null || preg_match('/^null$/i', $value)) {
            $value = 'NULL';
        } elseif (preg_match('/^(\'|")(.+)(\'|")$/', $value)) {
            $value = preg_replace("/(^('|\")|('|\")$)/", '', $value);
            $value = "'" . $this->escape($value) . "'";
        } elseif (($operand = $this->_prepareOperand($value))) {
            $value = $operand;
        } else {
            $value = "'" . $this->escape($value) . "'";
        }
        return $value;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->_current;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return 0;
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->_current = $this->asAssoc();
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        if (!$this->result) {
            $this->query();
        } else {
            $this->result->data_seek(0);
        }
        $this->_current = $this->asAssoc();
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->current() ? true : false;
    }
}
