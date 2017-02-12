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
     * Создание базы данных
     *
     * @return GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function create(): GDbDatabase
    {
        // TODO: Implement create() method.
    }

    /**
     * Удаление записей соответствующих критерию
     *
     * @param array $ctiteria
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function delete($ctiteria = []): int
    {
        // TODO: Implement delete() method.
    }

    /**
     * Удаление базы данных
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function drop()
    {
        // TODO: Implement drop() method.
    }

    /**
     * Экранирование спецсимволов и обрамление кавычками
     *
     * @param mixed $value
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function escape($value)
    {
        return $this->handler->real_escape_string($value);
    }

    /**
     * Устанавливает набо полей, возвращаемых в результате запроса
     *
     * @param array $fields
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function fields(array $fields): GDbCursor
    {

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
    }

    /**
     * Установка позиции и количества возвращаемых записей
     *
     * @param mixed $top
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function limit($top = null): GDbCursor
    {
        // TODO: Implement limit() method.
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
     * Выбор текущей базы данных
     *
     * @return GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function select(): GDbDatabase
    {
        // TODO: Implement select() method.
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
    }

    /**
     * Очистка таблицы от записей
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function truncate()
    {

    }

    /**
     * Обновление указанных полей для записей, соответствующих критерию
     *
     * @param null|array $criteria
     * @param array|object $properties
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public function update($criteria = [], $properties): int
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
    }
}