<?php

namespace Gear\Interfaces;

/**
 * Интерфейс коллекций базы данных (таблиц)
 *
 * @package Gear Framework
 *
 * @property mixed handler
 * @property string name
 * @property DbCursorInterface cursor
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface DbCollectionInterface
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
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCollection(): DbCollectionInterface;
    /**
     * Возвращает соединение с сервером базы данных
     *
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getConnection(): DbConnectionInterface;
    /**
     * Возвращает базу данных, в которой находится коллекция курсора
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getDatabase(): DbDatabaseInterface;

    /**
     * Добавление в коллекцию новой записи
     * Возвращает количество затронутых строк
     * В случае совпадения PRIMARY KEY генерируется исключение
     *
     * @param array|ModelInterface $properties
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function insert($properties): DbCollectionInterface;

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
     * @param null|array|ModelInterface $model
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function remove($model = []): DbCollectionInterface;

    /**
     * Очистка таблицы от записей
     *
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function truncate(): DbCollectionInterface;
}

/**
 * Интерфейс компонента подключения к базе данных
 *
 * @package Gear Framework
 *
 * @property mixed handler
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface DbConnectionInterface
{
    /**
     * Завершение соединения с сервером баз данных
     *
     * @abstract
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function close(): DbConnectionInterface;

    /**
     * Подготовка и вызов метода непосредственного подключения к серверу баз данных
     *
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function connect(): DbConnectionInterface;

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
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function open(): DbConnectionInterface;

    /**
     * Выполняет подключение к серверу, если соединение ещё не было
     * установлено
     *
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function reconnect(): DbConnectionInterface;

    /**
     * Выбор указанной базы данных и таблицы
     *
     * @param string $dbName
     * @param string $collectionName
     * @param string $alias
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function selectCollection(string $dbName, string $collectionName, string $alias = ''): DbCollectionInterface;

    /**
     * Выбор указанной базы данных
     *
     * @param string $name
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function selectDB(string $name): DbDatabaseInterface;
}

/**
 * Интерфейс запроса
 *
 * @package Gear Framework
 *
 * @property mixed handler
 * @property int lastInsertId
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface DbCursorInterface
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
     * @return ModelInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function asObject(string $class = '\Gear\Library\GModel'): ?ModelInterface;

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
     * @param array|ModelInterface $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function delete($criteria = []): DbCursorInterface;

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
     * Возвращает true, если указанный в критерии элемент существует в коллекции
     *
     * @param array|DbCursorInterface $criteria
     * @return bool
     * @since 0.0.1
     * @version 0.0.2
     */
    public function exists($criteria = []): bool;

    /**
     * Установка полей, значения которых вернуться при выполненинии запроса
     * @param $fields
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function fields($fields): DbCursorInterface;

    /**
     * Поиск записей по указханному критерию
     *
     * @param string|array $criteria
     * @param string|array $fields
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function find($criteria = [], $fields = []): DbCursorInterface;

    /**
     * Возвращает первую запись, соответствующую указанному критерию
     *
     * @param string|array $criteria
     * @param string|array $fields
     * @param array $sort
     * @param int $as
     * @return array|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function findOne($criteria = [], $fields = [], $sort = [], $as = 2);

    /**
     * Возвращает первые N элементов из запроса
     *
     * @param int $count
     * @return null|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function first(int $count = 1);

    /**
     * Возвращает коллекцию, для которой создан курсор
     *
     * @return DbCollectionInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCollection(): ?DbCollectionInterface;

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
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getConnection(): DbConnectionInterface;
    /**
     * Возвращает базу данных, в которой находится коллекция курсора
     *
     * @return DbDatabaseInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getDatabase(): ?DbDatabaseInterface;

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
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function group($group = []): DbCursorInterface;

    /**
     * Добавление в коллекцию новой записи
     * Возвращает количество затронутых строк
     * В случае совпадения PRIMARY KEY генерируется исключение
     *
     * @param array|ModelInterface $properties
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function insert($properties): DbCursorInterface;

    /**
     * Подключение таблицы
     *
     * @param string|array|DbCollectionInterface $collection
     * @param array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function join($collection, $criteria = []): DbCursorInterface;

    /**
     * Возвращает первые N элементов из запроса
     *
     * @param int $count
     * @return null|array
     * @since 0.0.1
     * @version 0.0.1
     */
    public function last(int $count = 1);

    /**
     * Левое подключение таблицы
     *
     * @param array|string|DbCollectionInterface $collection
     * @param array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function left($collection, array $criteria = []): DbCursorInterface;

    /**
     * Установка позиции и количества возвращаемых записей
     *
     * @param array $limit
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function limit(...$limit): DbCursorInterface;

    /**
     * Создание и выполнение запроса
     *
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function query(): DbCursorInterface;

    /**
     * Удаление записей, соответствующих критерию, либо найденных
     * в результате последнего выполненного SELECT-запроса
     *
     * @param null|array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove($criteria = []): DbCursorInterface;

    /**
     * Сброс результатов выполнения запроса
     *
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function reset(): DbCursorInterface;

    /**
     * Правое подключение таблицы
     *
     * @param string|DbCollectionInterface $collection
     * @param array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function right($collection, array $criteria = []): DbCursorInterface;

    /**
     * Выполнение составленного SQL-запроса
     *
     * @param string $query
     * @param array $params
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function runQuery(string $query, ...$params): DbCursorInterface;

    /**
     * Добавление в коллекцию новой записи. В случае совпадения
     * PRIMARY KEY происходит обновление указанных в $updates полей
     * записи
     * Возвращает количество затронутых полей
     *
     * @param array|ModelInterface $properties
     * @param array $updates
     * @return integer|object
     * @since 0.0.1
     * @version 0.0.2
     */
    public function save($properties, array $updates = []);

    /**
     * Установка сортировки результатов запроса
     *
     * @param string|array $sort
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function sort($sort = []): DbCursorInterface;

    /**
     * Обновление указанных полей для записей, соответствующих критерию
     *
     * @param null|string|array|ModelInterface $criteria
     * @param array $properties
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function update($criteria = [], array $properties = []): DbCursorInterface;

    /**
     * Формирование критерия поиска
     *
     * @param string|array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function where($criteria = []): DbCursorInterface;
}

/**
 * Интерфейс базы данных
 *
 * @package Gear Framework
 *
 * @property mixed handler
 * @property string name
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface DbDatabaseInterface
{
    /**
     * Возвращает соединение с сервером
     *
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getConnection(): DbConnectionInterface;

    /**
     * Создание базы данных
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function create(): DbDatabaseInterface;

    /**
     * Удаление базы данных
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function drop(): DbDatabaseInterface;

    /**
     * Удаление базы данных
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function remove(): DbDatabaseInterface;

    /**
     * Выбор текущей базы данных
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function select(): DbDatabaseInterface;

    /**
     * Возвращает коллекцию
     *
     * @param string $name
     * @param string $alias
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function selectCollection(string $name, string $alias = ''): DbCollectionInterface;
}

/**
 * Интерфейс компонентов, работающих с данными базы данных
 *
 * @package Gear Framework
 *
 * @property DbCursorInterface cursor
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface DbStorageComponentInterface
{
    /**
     * Добавление модели в набор (сохранение в коллекции-таблице в базе данных)
     *
     * @param ModelInterface|array of IModel $model
     * @return int
     * @since 0.0.1
     * @version 0.0.2
     */
    public function add($model): int;

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
     * @return ObjectInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function byPk($pkValue): ?ObjectInterface;

    /**
     * Возвращает количество элементов в коллекции, удовлетворяющих
     * критерию
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.2
     */
    public function count(): int;

    /**
     * Поиск моделей по указанному критерию
     *
     * @param array|string $criteria
     * @param array|string $fields
     * @return DbStorageComponentInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function find($criteria = [], $fields = []): DbStorageComponentInterface;

    /**
     * Поиск модели, соответствующей указанному критерию
     *
     * @param array|string $criteria
     * @return ObjectInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function findOne($criteria = []): ?ObjectInterface;

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
     * @return DbConnectionInterface
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getConnection(): DbConnectionInterface;

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
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCursor(): DbCursorInterface;

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
     * @return iterable
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getIterator();

    /**
     * Ограничение выборки
     *
     * @param mixed ...$limit
     * @return DbStorageComponentInterface
     * @since 0.0.2
     * @version 0.0.2
     */
    public function limit(...$limit): DbStorageComponentInterface;

    /**
     * Удаление модели
     *
     * @param array|ModelInterface|array of IModel $model
     * @since 0.0.1
     * @version 0.0.2
     * @return DbStorageComponentInterface
     */
    public function remove($model): DbStorageComponentInterface;

    /**
     * Сброс результатов выполнения запроса
     *
     * @return DbStorageComponentInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function reset(): DbStorageComponentInterface;

    /**
     * Сохранение модели
     *
     * @param array|ModelInterface|array of IModel $model
     * @return DbStorageComponentInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function save($model): DbStorageComponentInterface;

    /**
     * Выбор коллекции
     *
     * @param string $alias
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function selectCollection(string $alias = ""): DbCollectionInterface;

    /**
     * Выбор базы данных
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectDB(): DbDatabaseInterface;

    /**
     * Сортировка
     *
     * @param array $sort
     * @return DbStorageComponentInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function sort(array $sort): DbStorageComponentInterface;

    /**
     * Обновление существующей модели
     *
     * @param $model
     * @return DbStorageComponentInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function update($model): DbStorageComponentInterface;
}

/**
 * Интерфейс компонентов, работающих с данными базы данных
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
interface DbStoragePluginInterface extends DbStorageComponentInterface {}
