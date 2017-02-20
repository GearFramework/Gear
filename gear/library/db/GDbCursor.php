<?php

namespace gear\library\db;

use gear\interfaces\IModel;
use gear\library\GEvent;
use gear\library\GModel;

/**
 * Класс запроса
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class GDbCursor extends GModel implements \Iterator
{
    /* Traits */
    /* Const */
    const ASC = 1;
    const DESC = -1;
    /* Private */
    /* Protected */
    protected $_query = '';
    protected $_result = null;
    /* Public */

    /**
     * Возвращает количество строк, затронутых последним выполненным запросом
     * INSERT, UPDATE, DELETE
     *
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function affected(): int;

    /**
     * Событие, возникающее после исполнения запроса
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function afterQuery()
    {
        return $this->trigger('onAfterQuery', new GEvent($this, ['target' => $this]));
    }

    /**
     * Выполняет запрос и возвращает массив всех найденных записей
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function all(): array;

    /**
     * Выполняет запрос и возвращает ассоциативный массив найденной записи
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function asAssoc(): array;

    /**
     * Выполняет запрос и возвращает объект найденной записи, реализующий интерфейс IModel
     *
     * @return IModel
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function asObject(): IModel;

    /**
     * Выполняет запрос и возвращает индексный массив найденной записи
     *
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function asRow(): array;

    /**
     * Событие возникающее перед выполнением запроса
     *
     * @param string $query
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function beforeQuery(string $query)
    {
        return $this->trigger('onBeforeQuery', new GEvent($this, ['target' => $this, 'query' => $query]));
    }

    /**
     * Создание sql-запроса
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function buildQuery(): string;

    /**
     * Получение количества выбранных строк в результате выполнения запроса,
     * либо добавляет COUNT() внутрь SELECT запроса
     *
     * @param string $field
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function count(string $field);

    /**
     * Удаление записей соответствующих критерию
     *
     * @param null|array|IModel $ctiteria
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function delete($ctiteria = null): int;

    /**
     * Экранирование спецсимволов и обрамление кавычками
     *
     * @param mixed $value
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function escape($value);

    /**
     * Поиск записей по указханному критерию
     *
     * @param string|array $criteria
     * @param string|array $fields
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function find($criteria = [], $fields = []): GDbCursor;

    /**
     * Возвращает первую запись, соответствующую указанному критерию
     *
     * @param string|array $criteria
     * @param string|array $fields
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function findOne($criteria = [], $fields = []): array
    {
        return $this->find($criteria, $fields)->limit(1)->asAssoc();
    }

    /**
     * Возвращает первые N элементов из запроса
     *
     * @param int $count
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function first(int $count = 1): array
    {
        return $this->limit($count)->all();
    }

    /**
     * Возвращает коллекцию, для которой создан курсор
     *
     * @return GDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCollection(): GDbCollection
    {
        return $this->owner;
    }

    /**
     * Возвращает ссылку на компонент базы данных
     *
     * @return GDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnection(): GDbConnection
    {
        return $this->owner;
    }

    /**
     * Возвращает базу данных, в которой находится коллекция курсора
     *
     * @return GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDatabase(): GDbDatabase
    {
        return $this->owner->getDatabase();
    }

    /**
     * Возвращает ссылку на соединение с базой данных
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getHandler()
    {
        return $this->owner->getHandler();
    }

    /**
     * Возвращает ID последней вставленной записи
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function getLastInsertId(): int;

    /**
     * Возвращает строковое представление запроса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getQuery(): string
    {
        if (!$this->_query) {
            $this->query = $this->buildQuery();
        }
        return $this->_query;
    }

    /**
     * Возвращает результат последнего выполненного запроса
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Установка группировки результатов запроса
     *
     * @param string|array $group
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function group($group = ''): GDbCursor;

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
    abstract public function insert($properties): int;

    /**
     * Подключение таблицы
     *
     * @param string|object $collection
     * @param array $criteria
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function join($collection, $criteria = []): GDbCursor;

    /**
     * Возвращает первые N элементов из запроса
     *
     * @param int $count
     * @return array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function last(int $count = 1): array
    {
        return array_slice($this->all(), -$count, $count);
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
    abstract public function left($collection, array $criteria = []): GDbCursor;

    /**
     * Установка позиции и количества возвращаемых записей
     *
     * @param array $limit
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function limit(...$limit): GDbCursor;

    /**
     *
     * @param string|object $collection
     * @param array $criteria
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function outer($collection, array $criteria = []): GDbCursor;

    /**
     * Создание и выполнение запроса
     *
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function query(): GDbCursor
    {
        if ($this->beforeQuery($this->query)) {
            $this->runQuery($this->query);
            $this->afterQuery();
        }
        return $this;
    }

    /**
     * Удаление записей, соответствующих критерию, либо найденных
     * в результате последнего выполненного SELECT-запроса
     *
     * @param null|array $criteria
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove($criteria = []): int
    {
        return $this->delete($criteria);
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
    abstract public function right($collection, array $criteria = []): GDbCursor;

    /**
     * Выполнение составленного SQL-запроса
     *
     * @param string $query
     * @param array $params
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function runQuery(string $query, ...$params): GDbCursor;

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
    abstract public function save($properties, $updates = null): int;

    /**
     * Установка текущего запроса
     *
     * @param string $query
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setQuery(string $query)
    {
        $this->_query = $query;
    }

    /**
     * Установка результатов запроса
     *
     * @param mixed $result
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setResult($result)
    {
        $this->_result = $result;
    }

    /**
     * Установка сортировки результатов запроса
     *
     * @param string|array $sort
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function sort($sort = ''): GDbCursor;

    /**
     * Обновление указанных полей для записей, соответствующих критерию
     *
     * @param null|array $criteria
     * @param array|object $properties
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function update($criteria = [], $properties): int;

    /**
     * Формирование критерия поиска
     *
     * @param string|array $criteria
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function where($criteria = []): GDbCursor;
}
