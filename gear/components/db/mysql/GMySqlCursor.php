<?php

namespace gear\components\db\mysql;

use gear\interfaces\IModel;
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
        // TODO: Implement insert() method.
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
            $this->_queryBuild['limit'] = [0, reset($limit)];
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
