<?php

namespace Gear\Library\Db;

use Gear\Interfaces\IDbCollection;
use Gear\Interfaces\IDbConnection;
use Gear\Interfaces\IDbCursor;
use Gear\Interfaces\IDbDatabase;
use Gear\Interfaces\IModel;
use Gear\Library\GModel;
use Gear\Traits\TDelegateFactory;
use Gear\Traits\TFactory;

/**
 * Библиотека коллекций (таблиц)
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class GDbCollection extends GModel implements \IteratorAggregate, IDbCollection
{
    /* Traits */
    use TFactory;
    use TDelegateFactory;
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
     * @throws \CoreException
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
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCollection(): IDbCollection
    {
        return $this;
    }

    /**
     * Возвращает соединение с сервером базы данных
     *
     * @return IDbConnection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnection(): IDbConnection
    {
        return $this->getDatabase()->getConnection();
    }

    /**
     * Возвращает текущий выполняемый запрос
     *
     * @return null|IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCurrent(): ?IDbCursor
    {
        return $this->_current;
    }

    /**
     * Возвращает инстанс курсора для работы с запросами
     *
     * @return IDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCursor(): IDbCursor
    {
        $this->current = $this->factory($this->factoryProperties, $this);
        return $this->current;
    }

    /**
     * Возвращает базу данных, в которой находится коллекция курсора
     *
     * @return IDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDatabase(): IDbDatabase
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
     * @param null|IModel $model
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove(?IModel $model = null): IDbCollection
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
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function reset(): IDbCollection
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
     * @param IDbCursor $cursor
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setCurrent(IDbCursor $cursor)
    {
        $this->_current = $cursor;
    }

    /**
     * Очистка таблицы от записей
     *
     * @return IDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    abstract public function truncate(): IDbCollection;
}
