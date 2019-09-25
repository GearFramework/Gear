<?php

namespace Gear\Library\db;

use Gear\Interfaces\DbCollectionInterface;
use Gear\Interfaces\DbConnectionInterface;
use Gear\Interfaces\DbCursorInterface;
use Gear\Interfaces\DbDatabaseInterface;
use Gear\Interfaces\ModelInterface;
use Gear\Library\GEvent;
use Gear\Library\GModel;
use Gear\Traits\Factory\FactoryTrait;

/**
 * Класс запроса
 *
 * @package Gear Framework
 *
 * @property DbCollectionInterface collection
 * @property string collectionName
 * @property DbConnectionInterface connection
 * @property DbDatabaseInterface database
 * @property mixed handler
 * @property int lastInsertId
 * @property DbCollectionInterface|DbConnectionInterface|DbDatabaseInterface owner
 * @property string query
 * @property mixed result
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
abstract class GDbCursor extends GModel implements \Iterator, DbCursorInterface
{
    /* Traits */
    use FactoryTrait;
    /* Const */
    const ASC = 1;
    const DESC = -1;
    const AS_ROW = 1;
    const AS_ASSOC = 2;
    const AS_OBJECT = 3;
    /* Private */
    /* Protected */
    protected $_factoryProperties = [
        'class' => '\Gear\Library\GModel',
    ];
    protected $_model = [
        'class' => '\Gear\Library\GModel',
    ];
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
     * @return ModelInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function asObject(string $class = '\Gear\Library\GModel'): ?ModelInterface;

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
     * @param array|ModelInterface $criteria
     * @return DbCursorInterfacet
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function delete($criteria = []): DbCursorInterface;

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
     * Возвращает true, если элемент по указанному критерию существует в коллекции, иначе false
     *
     * @param mixed $criteria
     * @return bool
     * @since 0.0.1
     * @version 0.0.1
     */
    public function exists($criteria = []): bool
    {
        return $this->find($criteria)->count() > 0 ? true : false;
    }

    /**
     * Установка полей, значения которых вернуться при выполненинии запроса
     * @param $fields
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function fields($fields): DbCursorInterface;

    /**
     * Поиск записей по указханному критерию
     *
     * @param string|array $criteria
     * @param string|array $fields
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function find($criteria = [], $fields = []): DbCursorInterface;

    /**
     * Возвращает первую запись, соответствующую указанному критерию
     *
     * @param string|array|DbCursorInterface $criteria
     * @param string|array $fields
     * @param array $sort
     * @param int $as
     * @return iterable|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function findOne($criteria = [], $fields = [], $sort = [], $as = self::AS_ASSOC): ?iterable
    {
        if ($criteria instanceof DbCursorInterface) {
            /**
             * @var DbCursorInterface $criteria
             */
            return $criteria->fields($fields)->sort($sort)->limit(1)->asAssoc();
        } else {
            return $this->find($criteria, $fields)->sort($sort)->limit(1)->asAssoc();
        }
    }

    /**
     * Возвращает первые N элементов из запроса
     *
     * @param int $count
     * @param array $sort
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.1
     */
    public function first(int $count = 1, $sort = []): DbCursorInterface
    {
        return $this->sort($sort)->limit($count);
    }

    /**
     * Возвращает коллекцию, для которой создан курсор
     *
     * @return DbCollectionInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCollection(): ?DbCollectionInterface
    {
        return $this->owner instanceof DbCollectionInterface ? $this->owner : null;
    }

    /**
     * Возвращает название коллекции (таблицы), дял которой создан курсор
     *
     * @return string|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCollectionName(): ?string
    {
        return $this->owner instanceof DbCollectionInterface ? $this->owner->name : null;
    }

    /**
     * Возвращает ссылку на компонент подключения базы данных
     *
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getConnection(): DbConnectionInterface
    {
        return $this->owner->getConnection();
    }

    /**
     * Возвращает базу данных, в которой находится коллекция курсора
     *
     * @return DbDatabaseInterface|null
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getDatabase(): ?DbDatabaseInterface
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
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function group($group = []): DbCursorInterface;

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
    abstract public function insert($properties): DbCursorInterface;

    /**
     * Подключение таблицы
     *
     * @param string|DbCollectionInterface $collection
     * @param array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function join($collection, $criteria = []): DbCursorInterface;

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
     * @param string|DbCollectionInterface $collection
     * @param array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function left($collection, array $criteria = []): DbCursorInterface;

    /**
     * Установка позиции и количества возвращаемых записей
     *
     * @param array $limit
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function limit(...$limit): DbCursorInterface;

    public function onAfterConstruct(?GEvent $event = null)
    {
        $this->reset();
        return true;
    }

    /**
     * Создание и выполнение запроса
     *
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function query(): DbCursorInterface
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
     * @param null|array|ModelInterface $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function remove($criteria = []): DbCursorInterface
    {
        return $this->delete($criteria);
    }

    /**
     * Правое подключение таблицы
     *
     * @param string|DbCollectionInterface $collection
     * @param array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function right($collection, array $criteria = []): DbCursorInterface;

    /**
     * Выполнение составленного SQL-запроса
     *
     * @param string $query
     * @param array $params
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function runQuery(string $query, ...$params): DbCursorInterface;

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
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function sort($sort = []): DbCursorInterface;

    /**
     * Обновление указанных полей для записей, соответствующих критерию
     *
     * @param null|string|array|ModelInterface $criteria
     * @param array $properties
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function update($criteria = [], array $properties = []): DbCursorInterface;

    /**
     * Формирование критерия поиска
     *
     * @param string|array $criteria
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function where($criteria = []): DbCursorInterface;
}
