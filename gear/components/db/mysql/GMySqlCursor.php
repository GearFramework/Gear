<?php

namespace gear\components\db\mysql;

use gear\interfaces\IModel;
use gear\interfaces\IObject;
use gear\library\db\GDbCollection;
use gear\library\db\GDbCursor;
use gear\library\db\GDbDatabase;
use gear\library\GEvent;
use gear\library\GModel;
use gear\traits\TFactory;


/**
 * Класс для работы с mysql-запросами
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
class GMySqlCursor extends GDbCursor
{
    /* Traits */
    use TFactory;
    /* Const */
    /* Private */
    /* Protected */
    protected $_queryBuild = [];
    protected $_factoryQueryBuild = [
        'class' => '\gear\library\GModel',
        'fields' => [],
        'from' => null,
        'where' => [],
        'group' => [],
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
        return $this->result->fetch_all(MYSQLI_BOTH);
    }

    /**
     * Выполняет запрос и возвращает ассоциативный массив найденной записи
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function asAssoc(): array
    {
        if (!$this->result) {
            $this->query();
        }
        return $this->result->fetch_assoc();
    }

    /**
     * Выполняет запрос и возвращает объект найденной записи, реализующий интерфейс IModel
     *
     * @return IModel
     * @since 0.0.1
     * @version 0.0.1
     */
    public function asObject(): IModel
    {
        if (!$this->result) {
            $this->query();
        }
        return ($properties = $this->result->fetch_assoc()) ? new GModel($properties) : false;
    }

    /**
     * Выполняет запрос и возвращает индексный массив найденной записи
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function asRow(): array
    {
        if (!$this->result) {
            $this->query();
        }
        return $this->result->fetch_array(MYSQLI_NUM);
    }

    /**
     * Создание sql-запроса
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function buildQuery(): string
    {
        $this->_query = 'SELECT SQL_CALC_FOUND_ROWS ' . implode(', ', $this->_queryBuild->fields)
                      . ' FROM ' . $this->_queryBuild->from;
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
     * @param null|array|IModel $criteria
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function delete($criteria = null): int
    {
        if (!$criteria) {

        } else if (is_array($criteria)) {
            $criteria = $this->_prepareCriteria($criteria);
        } else if ($criteria instanceof IModel) {
            $pk = $criteria->primaryKey;
            $query = 'DELETE FROM `' . $this->getCollectionName() . "` WHERE `$pk` = " . $criteria->$pk;
        } else {
            throw new \InvalidArgumentException('Invalid arguments to delete');
        }
        $this->runQuery($query)->affected();
        return $this;
    }

    /**
     * Экранирование спецсимволов и обрамление кавычками
     *
     * @param string $value
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function escape($value)
    {
        return $this->handler->real_escape_string($value);
    }

    /**
     * Устанавливает набор полей, возвращаемых в результате запроса
     *
     * $this->fields(['col1', 'col2' => -1, 'col3' => 1, 'col4 as field4', 'MAX(col5)']);
     *
     * @param string|array $fields
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function fields($fields): GDbCursor
    {
        $tempFields = $this->_queryBuild->fields;
        if (is_array($fields)) {
            foreach($fields as $name => $entry) {
                if (is_numeric($name) || $entry > 0) {
                    if (!strpos($name, '.') && !preg_match('/\s(as)\s/i', $name) &&
                        strpos($name, '(') === false) {
                        $name = "`$name`";
                    }
                    $tempFields[] = $name;
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
     * @param array $criteria
     * @param array $fields
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function find(array $criteria = [], array $fields = []): GDbCursor
    {
        $this->reset();
        $this->_queryBuild->from = '`' . $this->getCollectionName() . '`';
        return $this->fields($fields)->where($criteria);
    }

    /**
     * Возвращает название коллекции (таблицы), дял которой создан курсор
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCollectionName(): string
    {
        return $this->owner->name;
    }

    /**
     * Возвращает данные создаваемого объекта
     *
     * @param array $record
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getFactory(array $record = []): array
    {
        return $record ? array_replace_recursive($this->_factoryQueryBuild, $record) : $this->_factoryQueryBuild;
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
            $this->runQuery($this->buildQuery());
        }
        return $this->handler->insert_id;
    }

    /**
     * Установка группировки результатов запроса
     *
     * @param string|array $group
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function group($group = ''): GDbCursor
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
     * Добавление в коллекцию новой записи
     * Возвращает количество затронутых строк
     * В случае совпадения PRIMARY KEY генерируется исключение
     *
     * @param mixed $properties
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public function insert($properties): int
    {
        $this->reset();
        if ($properties instanceof IObject) {
            $properties = $properties->props();
        } else if (is_object($properties)) {
            $properties = get_class_vars(get_class($properties));
        } else if (!is_array($properties)) {
            throw new \InvalidArgumentException('Invalid properties to insert');
        }
        list($names, $values) = $this->_prepareInsert($properties);
        $this->runQuery("INSERT INTO `" . $this->getCollectionName() . "` $names VALUES $values");
        return $this->affected();
    }

    /**
     * $this->where(['a' => 2]);
     * $this->where(['a' => ['&lt' => 2]]);
     * $this->where(['a' => [2, 3, 4]]);
     * $this->where(['a' => ['$lt' => 2, '$or' => 3]]);
     * $this->where(['a' => ['$lt' => 2, '$or' => ['$gt' => 7]]]);
     * $this->where([['a' => 2, '$and' => ['b' => 3]]]);
     *
     * @param array $criteria
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    private function _prepareCriteria(array $criteria, $logic = 'AND', $op = null, $eq = '='): string
    {
        $result = [];
        $currentRow = '';
        foreach($criteria as $left => $right) {
            if (is_numeric($left)) {
                $result[] = '(' . $this->_prepareCriteria($right, $logic) . ')';
            } else if (in_array($left, ['$or', '$and'])) {

            } else if (in_array($left, ['$lt', '$gt', '$ne', '$lte', '$gte'])) {

            } else if ($left === '$in') {
                $result[] = " $logic $op IN " . implode(', ', $this->_prepareValue($right)) . ')';
            } else if ($left === '$nin') {
                $result[] = " $logic $op NOT IN " . implode(', ', $this->_prepareValue($right)) . ')';
            } else {
                if (ArrayHelper::isAssoc($right)) {
                    $result[] = "$left IN (" . implode(', ', $this->_prepareValue($right)) . ')';
                } else {
                    $result[] = " $logic " . $this->_prepareCriteria($right, $logic, $left);
                }
            }
        }
        return implode(" $logic ", $result);
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
         * ArrayHelper это алиас класса \gear\helpers\HArray
         */
        if (ArrayHelper::isAssoc($properties)) {
            $names = array_keys($properties);
            foreach($properties as &$value) {
                $value = '"' . $this->escape($value) . '"';
            }
            unset($value);
        } else {
            $names = array_keys(reset($properties));
            $properties;
            foreach($properties as $index => $p) {
                foreach($p as &$value) {
                    $value = '"' . $this->escape($value) . '"';
                }
                unset($value);
                $properties[$index] = '(' . implode(', ', $p) . ')';
            }
        }
        $names = '`' . implode('`, `', $names) . '`';
        $properties = implode(', ', $properties);
        return [$names, $properties];
    }

    private function _prepareValue($value)
    {
        if (is_array($value)) {
            foreach($value as &$val) {
                $val = $this->_prepareValue($val);
            }
            unset($val);
        } else {
            $value = "'" . $this->escape($value) . "'";
        }
        return $value;
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
    public function join($collection, $criteria = []): GDbCursor
    {
        // TODO: Implement join() method.
        return $this;
    }

    /**
     * Левое подключение таблицы
     *
     * @param string|object $collection
     * @param array $criteria
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function left($collection, array $criteria = []): GDbCursor
    {
        // TODO: Implement left() method.
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
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function limit(...$limit): GDbCursor
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
     *
     * @param string|object $collection
     * @param array $criteria
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function outer($collection, array $criteria = []): GDbCursor
    {
        // TODO: Implement outer() method.
    }

    /**
     * Сброс результатов выполнения последнего запроса
     *
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function reset(): GDbCursor
    {
        if ($this->result) {
            $this->result->free();
            $this->result = null;
        }
        $this->query = null;
        $this->_queryBuild = $this->factory([], $this);
        $cursor = $this;
        $this->_queryBuild->builderExecute = function() use ($cursor) {
            $cursor->buildQuery();
        };
        return $this;
    }

    /**
     * Правое подключение таблицы
     *
     * @param string|object $collection
     * @param array $criteria
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function right($collection, array $criteria = []): GDbCursor
    {
        // TODO: Implement right() method.
    }

    /**
     * Выполнение составленного SQL-запроса
     *
     * @param string $query
     * @param array $params
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function runQuery(string $query, ...$params): GDbCursor
    {
        if ($params) {
            $bindParams = [];
            foreach($params as $param) {
                $bindParams[] = $this->eascape($param);
            }
            $query = sprintf($query, ...$bindParams);
        }
        if (!$this->result = $this->handler->query($query)) {
            throw self::exceptionDbCursor('Invalid run query', ['query' => $query]);
        }
        return $this;
    }

    /**
     * Добавление в коллекцию новой записи. В случае совпадения
     * PRIMARY KEY происходит обновление указанных в $updates полей
     * записи
     * Возвращает количество затронутых полей
     *
     * @param mixed $properties
     * @param mixed $updates
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public function save($properties, $updates = null): int
    {
        // TODO: Implement save() method.
    }

    /**
     * Установка сортировки результатов запроса
     *
     * @param string|array $sort
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function sort($sort = ''): GDbCursor
    {
        $tempSort = $this->_queryBuild->sort;
        if (is_array($sort)) {
            foreach($sort as $col => &$order) {
                if (!is_numeric($col)) {
                    $order = "$col " . ($order === self::ASC ? 'ASC' : 'DESC');
                }
            }
            $tempSort = array_merge($tempSort, $sort);
        } else {
            $tempSort[] = $tempSort;
        }
        $this->_queryBuild->sort = $tempSort;
        return $this;
    }

    /**
     * Обновление указанных полей для записей, соответствующих критерию
     *
     * @param array|object $criteria
     * @param array|object $properties
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public function update($criteria = [], $properties = []): int
    {
        // TODO: Implement update() method.
    }

    /**
     * Формирование критерия поиска
     *
     * @param null|array $criteria
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function where(array $criteria = []): GDbCursor
    {
        // TODO: Implement where() method.
        return $this;
    }
}
