<?php

namespace Gear\Library\db;

use Gear\Interfaces\IDbCollection;
use Gear\Interfaces\IDbConnection;
use Gear\Interfaces\IDbCursor;
use Gear\Interfaces\IDbDatabase;
use Gear\Interfaces\IModel;
use Gear\Library\GEvent;
use Gear\Library\GModel;

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
abstract class GDbCursor extends GModel implements \Iterator, IDbCursor
{
    /* Traits */
    /* Const */
    const ASC = 1;
    const DESC = -1;
    const AS_ROW = 1;
    const AS_ASSOC = 2;
    const AS_OBJECT = 3;
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
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function all(): iterable;

    /**
     * Выполняет запрос и возвращает ассоциативный массив найденной записи
     *
     * @return iterable|null
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function asAssoc(): ?iterable;

    /**
     * Выполняет запрос и возвращает объект найденной записи, реализующий интерфейс IModel
     *
     * @param string $class
     * @return IModel|null
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function asObject(string $class = '\Gear\Library\GModel'): ?IModel;

    /**
     * Выполняет запрос и возвращает индексный массив найденной записи
     *
     * @return iterable|null
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function asRow(): ?iterable;

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
     *
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
    abstract public function count(string $field = '*');

    /**
     * Удаление записей соответствующих критерию
     *
     * @param array|IModel $criteria
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function delete($criteria = []): int;

    /**
     * Экранирование спецсимволов и обрамление кавычками
     *
     * @param mixed $value
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function escape($value): string;

    /**
     * Поиск записей по указханному критерию
     *
     * @param string|array $criteria
     * @param string|array $fields
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function find($criteria = [], $fields = []): IDbCursor;

    /**
     * Возвращает первую запись, соответствующую указанному критерию
     *
     * @param string|array $criteria
     * @param string|array $fields
     * @param int $as
     * @return array|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function findOne($criteria = [], $fields = [], $as = self::AS_ASSOC)
    {
        return $this->find($criteria, $fields)->limit(1)->asAssoc();
    }

    /**
     * Возвращает первые N элементов из запроса
     *
     * @param int $count
     * @param array $sort
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function first(int $count = 1, $sort = []): iterable
    {
        return $this->sort($sort)->limit($count);
    }

    /**
     * Возвращает коллекцию, для которой создан курсор
     *
     * @return IDbCollection|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCollection(): ?IDbCollection
    {
        return $this->owner instanceof IDbCollection ? $this->owner : null;
    }

    /**
     * Возвращает название коллекции (таблицы), дял которой создан курсор
     *
     * @return string|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCollectionName(): ?string
    {
        return $this->owner instanceof IDbCollection ? $this->owner->name : null;
    }

    /**
     * Возвращает ссылку на компонент подключения базы данных
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnection(): IDbConnection
    {
        return $this->owner->getConnection();
    }

    /**
     * Возвращает базу данных, в которой находится коллекция курсора
     *
     * @return IDbDatabase|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDatabase(): ?IDbDatabase
    {
        return method_exists($this->owner, 'getDatabase') ? $this->owner->getDatabase() : null;
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
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function group($group = []): IDbCursor;

    /**
     * Добавление в коллекцию новой записи
     * Возвращает количество затронутых строк
     * В случае совпадения PRIMARY KEY генерируется исключение
     *
     * @param array|IModel $properties
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function insert($properties): int;

    /**
     * Подключение таблицы
     *
     * @param string|IDbCollection $collection
     * @param array $criteria
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function join($collection, $criteria = []): IDbCursor;

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
     * @param string|IDbCollection $collection
     * @param array $criteria
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function left($collection, array $criteria = []): IDbCursor;

    /**
     * Установка позиции и количества возвращаемых записей
     *
     * @param array $limit
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function limit(...$limit): IDbCursor;

    public function onAfterConstruct($event)
    {
        $this->reset();
        return true;
    }

    /**
     * Создание и выполнение запроса
     *
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function query(): IDbCursor
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
     * @param null|array|IModel $criteria
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
     * @param string|IDbCollection $collection
     * @param array $criteria
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function right($collection, array $criteria = []): IDbCursor;

    /**
     * Выполнение составленного SQL-запроса
     *
     * @param string $query
     * @param array $params
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function runQuery(string $query, ...$params): IDbCursor;

    /**
     * Добавление в коллекцию новой записи. В случае совпадения
     * PRIMARY KEY происходит обновление указанных в $updates полей
     * записи
     * Возвращает количество затронутых полей
     *
     * @param array|IModel $properties
     * @param array $updates
     * @return integer|object
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function save($properties, array $updates = []);

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
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function sort($sort = []): IDbCursor;

    /**
     * Обновление указанных полей для записей, соответствующих критерию
     *
     * @param null|string|array|IModel $criteria
     * @param array $properties
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function update($criteria = [], array $properties = []): int;

    /**
     * Формирование критерия поиска
     *
     * @param string|array $criteria
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function where($criteria = []): IDbCursor;
}
