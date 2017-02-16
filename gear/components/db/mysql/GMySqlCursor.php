<?php

namespace gear\components\db\mysql;

use gear\interfaces\IModel;
use gear\interfaces\IObject;
use gear\library\db\GDbCollection;
use gear\library\db\GDbCursor;
use gear\library\db\GDbDatabase;
use gear\library\GModel;


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
    /* Const */
    /* Private */
    /* Protected */
    protected $_queryBuild = [
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
        // TODO: Implement buildQuery() method.
    }

    /**
     * Получение количества выбранных строк в результате выполнения запроса,
     * либо добавляет COUNT() внутрь SELECT запроса
     *
     * @param null|string|array $field
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public function count($field = null): int
    {
        // TODO: Implement count() method.
    }

    /**
     * Удаление записей соответствующих критерию
     *
     * @param array|object $criteria
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function delete($criteria = []): int
    {
        // TODO: Implement delete() method.
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
     * @param array $fields
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function fields(array $fields): GDbCursor
    {
        return $this;
    }

    /**
     * Поиск записей по указханному критерию
     *
     * @param array $criteria
     * @param array $fields
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function find(array $criteria = [], array $fields = []): GDbCursor
    {
        // TODO: Implement find() method.
        return $this;
    }

    public function getCollectionName(): string
    {
        return $this->owner->name;
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
        // TODO: Implement getLastInsertId() method.
    }

    /**
     * Установка группировки результатов запроса
     *
     * @param null|string|array $group
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function group($group = null): GDbCursor
    {
        // TODO: Implement group() method.
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

    private function _prepareInsert(array $properties)
    {
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
            $this->_queryBuild['limit'] = [0, 1];
        } else if (count($limit) === 1) {
            $limit = reset($limit);
            is_array($limit) ? $this->limit(...$limit) : $this->_queryBuild['limit'] = [0, $limit];
        } else if (count($limit) > 1) {
            list($this->_queryBuild['limit'][0], $this->_queryBuild['limit'][1]) = $limit;
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
     * @param null|string|array $sort
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function sort($sort = null): GDbCursor
    {
        // TODO: Implement sort() method.
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
