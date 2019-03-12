<?php

namespace Gear\Library\Db;

use Gear\Interfaces\DbCollectionInterface;
use Gear\Interfaces\DbConnectionInterface;
use Gear\Interfaces\DbCursorInterface;
use Gear\Interfaces\DbDatabaseInterface;
use Gear\Interfaces\ModelInterface;
use Gear\Library\GModel;
use Gear\Traits\Factory\DelegateFactoryTrait;
use Gear\Traits\Factory\FactoryTrait;

/**
 * Библиотека коллекций (таблиц)
 *
 * @package Gear Framework
 *
 * @property string alias
 * @property DbConnectionInterface connection
 * @property null|DbCursorInterface current
 * @property DbCursorInterface cursor
 * @property DbDatabaseInterface database
 * @property string name
 * @property DbDatabaseInterface owner
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
abstract class GDbCollection extends GModel implements \IteratorAggregate, DbCollectionInterface
{
    /* Traits */
    use DelegateFactoryTrait;
    use FactoryTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected $_alias = '';
    protected $_current = null;
    protected $_factoryProperties = [
        'class' => '\Gear\Library\Db\GDbCursor',
    ];
    protected $_items = [];
    /* Public */

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed|null
     * @throws \ComponentNotFoundException
     * @throws \CoreException
     * @throws \PluginNotFoundException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function __call(string $name, array $arguments)
    {
        $result = null;
        if (method_exists($this->factoryProperties['class'], $name)) {
            $result = $this->cursor->$name(... $arguments);
        } else {
            $result = parent::__call($name, $arguments);
        }
        return $result;
    }

    /**
     * Удаление коллекции
     *
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function drop();

    /**
     * @param array $criteria
     * @param array $fields
     * @return DbCursorInterface
     * @since 0.0.2
     * @version 0.0.2
     */
    public function find($criteria = [], $fields = []): DbCursorInterface
    {
        return $this->cursor->find($criteria, $fields);
    }

    /**
     * @param array $criteria
     * @param array $fields
     * @param array $sort
     * @param int $as
     * @return array
     * @since 0.0.2
     * @version 0.0.2
     */
    public function findOne($criteria = [], $fields = [], $sort = [], $as = GDbCursor::AS_ASSOC): array
    {
        return $this->cursor->findOne($criteria, $fields, $sort, $as);
    }

    /**
     * Возвращает псевдоним для коллекции
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getAlias(): string
    {
        return $this->_alias;
    }

    /**
     * Возвращает коллекцию, т.е. саму себя
     *
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCollection(): DbCollectionInterface
    {
        return $this;
    }

    /**
     * Возвращает соединение с сервером базы данных
     *
     * @return DbConnectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getConnection(): DbConnectionInterface
    {
        return $this->getDatabase()->getConnection();
    }

    /**
     * Возвращает текущий выполняемый запрос
     *
     * @return null|DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCurrent(): ?DbCursorInterface
    {
        return $this->_current;
    }

    /**
     * Возвращает инстанс курсора для работы с запросами
     *
     * @return DbCursorInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getCursor(): DbCursorInterface
    {
        $this->current = $this->factory($this->factoryProperties, $this);
        return $this->current;
    }

    /**
     * Возвращает базу данных, в которой находится коллекция курсора
     *
     * @return DbDatabaseInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getDatabase(): DbDatabaseInterface
    {
        return $this->owner;
    }

    /**
     * Возвращает ресурс соединения с сервером базы данных
     *
     * @return mixed
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getHandler()
    {
        return $this->getConnection()->handler;
    }

    /**
     * Добавление в коллекцию новой записи
     * Возвращает количество затронутых строк
     * В случае совпадения PRIMARY KEY генерируется исключение
     *
     * @param array|ModelInterface $properties
     * @return integer
     * @since 0.0.1
     * @version 0.0.2
     */
    public function insert($properties): int
    {
        return $this->cursor->insert($properties);
    }

    /**
     * Возвращает ID последней вставленной записи в таблицу
     *
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function lastInsertId(): int
    {
        return $this->current ? $this->current->lastInsertId : 0;
    }

    /**
     * Удаление таблицы или указанной модели из таблицы
     *
     * @param null|array|ModelInterface $model
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function remove($model = []): DbCollectionInterface
    {
        if ($model) {
            $this->cursor->remove($model);
        } else {
            $this->drop();
        }
        return $this;
    }

    /**
     * Сброс результатов выполнения последнего запроса
     *
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    public function reset(): DbCollectionInterface
    {
        if ($this->current) {
            $this->current->reset();
        }
        return $this;
    }

    /**
     * Установка псевдонима для коллекции
     *
     * @param string $alias
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setAlias(string $alias)
    {
        $this->_alias = $alias;
    }

    /**
     * Установка текущего выполняемого запроса
     *
     * @param DbCursorInterface $cursor
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setCurrent(DbCursorInterface $cursor)
    {
        $this->_current = $cursor;
    }

    /**
     * Очистка таблицы от записей
     *
     * @return DbCollectionInterface
     * @since 0.0.1
     * @version 0.0.2
     */
    abstract public function truncate(): DbCollectionInterface;
}
