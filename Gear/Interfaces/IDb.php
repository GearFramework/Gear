<?php

namespace Gear\Interfaces;

/**
 * Интерфейс компонента подключения к базе данных
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IDbConnection
{
    /**
     * Завершение соединения с сервером баз данных
     *
     * @abstract
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function close(): IDbConnection;

    /**
     * Подготовка и вызов метода непосредственного подключения к серверу баз данных
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function connect(): IDbConnection;

    /**
     * Возвращает true если соединение уже установлено, иначе false
     *
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function isConnected(): bool;

    /**
     * Подключение к серверу базы данных
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function open(): IDbConnection;

    /**
     * Выполняет подключение к серверу, если соединение ещё не было
     * установлено
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function reconnect(): IDbConnection;

    /**
     * Выбор указанной базы данных и таблицы
     *
     * @param string $dbName
     * @param string $collectionName
     * @param string $alias
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectCollection(string $dbName, string $collectionName, string $alias = ''): IDbCollection;

    /**
     * Выбор указанной базы данных
     *
     * @param string $name
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectDB(string $name): IDbDatabase;
}

/**
 * Интерфейс базы данных
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IDbDatabase
{
    /**
     * Создание базы данных
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function create(): IDbDatabase;

    /**
     * Удаление базы данных
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function drop(): IDbDatabase;

    /**
     * Удаление базы данных
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove(): IDbDatabase;

    /**
     * Выбор текущей базы данных
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function select(): IDbDatabase;

    /**
     * Возвращает коллекцию
     *
     * @param string $name
     * @param string $alias
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectCollection(string $name, string $alias = ''): IDbCollection;
}

/**
 * Интерфейс коллекций базы данных (таблиц)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IDbCollection
{
    /**
     * Удаление базы коллекции
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function drop();

    /**
     * Возвращает коллекцию, т.е. саму себя
     *
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCollection(): IDbCollection;
    /**
     * Возвращает соединение с сервером базы данных
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnection(): IDbConnection;
    /**
     * Возвращает базу данных, в которой находится коллекция курсора
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDatabase(): IDbDatabase;

    /**
     * Возвращает ID последней вставленной записи в таблицу
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function lastInsertId(): int;

    /**
     * Удаление таблицы или указанной модели из таблицы
     *
     * @param null|IModel $model
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove(?IModel $model = null): IDbCollection;

    /**
     * Очистка таблицы от записей
     *
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function truncate(): IDbCollection;
}

/**
 * Интерфейс запроса
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IDbCursor
{
    /**
     * Возвращает количество строк, затронутых последним выполненным запросом
     * INSERT, UPDATE, DELETE
     *
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public function affected(): int;

    /**
     * Выполняет запрос и возвращает массив всех найденных записей
     *
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function all(): iterable;

    /**
     * Выполняет запрос и возвращает ассоциативный массив найденной записи
     *
     * @return iterable|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function asAssoc(): ?iterable;

    /**
     * Выполняет запрос и возвращает объект найденной записи, реализующий интерфейс IModel
     *
     * @param string $class
     * @return IModel|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function asObject(string $class = '\Gear\Library\GModel'): ?IModel;

    /**
     * Выполняет запрос и возвращает индексный массив найденной записи
     *
     * @return iterable|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function asRow(): ?iterable;

    /**
     * Создание sql-запроса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function buildQuery(): string;

    /**
     * Получение количества выбранных строк в результате выполнения запроса,
     * либо добавляет COUNT() внутрь SELECT запроса
     *
     * @param string $field
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function count(string $field = '*');

    /**
     * Удаление записей соответствующих критерию
     *
     * @param array|IModel $criteria
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function delete($criteria = []): int;

    /**
     * Экранирование спецсимволов и обрамление кавычками
     *
     * @param mixed $value
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function escape($value): string;

    /**
     * Поиск записей по указханному критерию
     *
     * @param string|array $criteria
     * @param string|array $fields
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function find($criteria = [], $fields = []): IDbCursor;

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
    public function findOne($criteria = [], $fields = [], $as = self::AS_ASSOC);

    /**
     * Возвращает первые N элементов из запроса
     *
     * @param int $count
     * @param array $sort
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function first(int $count = 1, $sort = []): iterable;

    /**
     * Возвращает коллекцию, для которой создан курсор
     *
     * @return IDbCollection|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCollection(): ?IDbCollection;

    /**
     * Возвращает название коллекции (таблицы), дял которой создан курсор
     *
     * @return string|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCollectionName(): ?string;

    /**
     * Возвращает ссылку на компонент подключения базы данных
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnection(): IDbConnection;
    /**
     * Возвращает базу данных, в которой находится коллекция курсора
     *
     * @return IDbDatabase|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDatabase(): ?IDbDatabase;

    /**
     * Возвращает ID последней вставленной записи
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getLastInsertId(): int;

    /**
     * Возвращает строковое представление запроса
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getQuery(): string;

    /**
     * Установка группировки результатов запроса
     *
     * @param string|array $group
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function group($group = []): IDbCursor;

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
    public function insert($properties): int;

    /**
     * Подключение таблицы
     *
     * @param string|IDbCollection $collection
     * @param array $criteria
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function join($collection, $criteria = []): IDbCursor;

    /**
     * Возвращает первые N элементов из запроса
     *
     * @param int $count
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function last(int $count = 1): iterable;

    /**
     * Левое подключение таблицы
     *
     * @param string|IDbCollection $collection
     * @param array $criteria
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function left($collection, array $criteria = []): IDbCursor;

    /**
     * Установка позиции и количества возвращаемых записей
     *
     * @param array $limit
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function limit(...$limit): IDbCursor;

    /**
     * Создание и выполнение запроса
     *
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function query(): IDbCursor;

    /**
     * Удаление записей, соответствующих критерию, либо найденных
     * в результате последнего выполненного SELECT-запроса
     *
     * @param null|array $criteria
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove($criteria = []): int;

    /**
     * Правое подключение таблицы
     *
     * @param string|IDbCollection $collection
     * @param array $criteria
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function right($collection, array $criteria = []): IDbCursor;

    /**
     * Выполнение составленного SQL-запроса
     *
     * @param string $query
     * @param array $params
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function runQuery(string $query, ...$params): IDbCursor;

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
    public function save($properties, array $updates = []);

    /**
     * Установка сортировки результатов запроса
     *
     * @param string|array $sort
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function sort($sort = []): IDbCursor;

    /**
     * Обновление указанных полей для записей, соответствующих критерию
     *
     * @param null|string|array|IModel $criteria
     * @param array $properties
     * @return integer
     * @since 0.0.1
     * @version 0.0.1
     */
    public function update($criteria = [], array $properties = []): int;

    /**
     * Формирование критерия поиска
     *
     * @param string|array $criteria
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function where($criteria = []): IDbCursor;
}

/**
 * Интерфейс компонентов, работающих с данными базы данных
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
interface IDbStorageComponent
{
    /**
     * Добавление модели в набор (сохранение в коллекции-таблице в базе данных)
     *
     * @param IModel|array of IModel $model
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function add($model): int;

    /**
     * Выборка всех моделей из коллекции
     *
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function all(): iterable;

    /**
     * Выборка модели по значению первичного ключа
     *
     * @param int|string $pkValue
     * @return \Gear\Interfaces\IObject|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function byPk($pkValue);

    /**
     * Поиск моделей по указанному критерию
     *
     * @param array|string $criteria
     * @param array|string $fields
     * @return \Iterator
     * @since 0.0.1
     * @version 0.0.1
     */
    public function find($criteria = [], $fields = []);

    /**
     * Поиск модели, соответствующей указанному критерию
     *
     * @param array|string $criteria
     * @return \Gear\Interfaces\IObject|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function findOne($criteria = []);

    /**
     * Возвращает название таблицы
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCollectionName(): string;

    /**
     * Возвращает компонент подключения к базе данных
     *
     * @return IDbConnection
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnection(): IDbConnection;

    /**
     * Возвращает название компонента подключения к серверу базы данных
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnectionName(): string;

    /**
     * Возвращает курсор коллекции
     *
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCursor(): IDbCursor;

    /**
     * Возвращает название базы данных
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDbName(): string;

    /**
     * Возвращает итератор со записями
     *
     * @param mixed $cursor
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getIterator($cursor = null): iterable;

    /**
     * Удаление модели
     *
     * @param array|IModel|array of IModel $model
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove($model);

    /**
     * Сохранение модели
     *
     * @param array|IModel|array of IModel $model
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function save($model): int;

    /**
     * Выбор коллекции
     *
     * @param string $alias
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectCollection(string $alias = ""): IDbCollection;
    /**
     * Выбор базы данных
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectDB(): IDbDatabase;

    /**
     * Обновление существующей модели
     *
     * @param $model
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function update($model): int;
}