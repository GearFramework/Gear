<?php

namespace Gear\Library\Db;

use Gear\Core;
use Gear\Interfaces\IModel;
use Gear\Library\GComponent;
use Gear\Traits\TDelegateFactory;
use Gear\Traits\TFactory;

/**
 * Бибилиотека для компонентов, работающих с данными базы данных
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.1
 */
abstract class GDbStorageComponent extends GComponent implements \IteratorAggregate
{
    /* Traits */
    use TFactory;
    use TDelegateFactory;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_initialized = false;
    protected $_factory = [
        'class' => '\gear\library\GModel',
    ];
    protected $_connection = null;
    protected $_connectionName = 'db';
    protected $_dbName = '';
    protected $_collectionName = '';
    protected $_defaultParams = [];
    /* Public */

    /**
     * Добавление модели в набор (сохранение в коллекции-таблице в базе данных)
     *
     * @param IModel $model
     * @return $this
     * @since 0.0.1
     * @version 0.0.1
     */
    public function add(IModel $model)
    {
        $this->selectCollection()->insert($model);
        return $this;
    }

    /**
     * Выборка всех моделей из коллекции
     *
     * @return \Iterator
     * @since 0.0.1
     * @version 0.0.1
     */
    public function all()
    {
        return $this->find();
    }

    /**
     * Выборка модели по значению первичного ключа
     *
     * @param int|string $pkValue
     * @return \Gear\Interfaces\IObject|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function byPk($pkValue)
    {
        $class = $this->factory['class'];
        $result = $this->selectCollection()->findOne([$class::primaryKey() => "'$pkValue'"]);
        return $result ? $this->factory($result) : $result;
    }

    /**
     * Поиск моделей по указанному критерию
     *
     * @param array|string $criteria
     * @param array|string $fields
     * @return \Iterator
     * @since 0.0.1
     * @version 0.0.1
     */
    public function find($criteria = [], $fields = [])
    {
        return $this->getIterator($this->selectCollection()->find($criteria, $fields));
    }

    /**
     * Поиск модели, соответствующей указанному критерию
     *
     * @param array|string $criteria
     * @return \Gear\Interfaces\IObject|null
     * @since 0.0.1
     * @version 0.0.1
     */
    public function findOne($criteria = [])
    {
        $result = $this->selectCollection()->findOne($criteria);
        return $result ? $this->factory($result) : $result;
    }

    /**
     * Возвращает название таблицы
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCollectionName(): string
    {
        return $this->_collectionName;
    }

    /**
     * Возвращает компонент подключения к базе данных
     *
     * @return GDbConnection
     * @since 0.0.1
     * @version 0.0.1
     * @throws \CoreException
     */
    public function getConnection(): GDbConnection
    {
        if (!$this->_connection) {
            $this->_connection = Core::c($this->connectionName);
        }
        return $this->_connection;
    }

    /**
     * Возвращает название компонента подключения к серверу базы данных
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getConnectionName(): string
    {
        return $this->_connectionName;
    }

    /**
     * Возвращает курсор коллекции
     *
     * @return GDbCursor
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getCursor(): GDbCursor
    {
        return $this->selectCollection()->cursor;
    }

    /**
     * Возвращает название базы данных
     *
     * @return string
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getDbName(): string
    {
        return $this->_dbName;
    }

    /**
     * Возвращает итератор со записями
     *
     * @param mixed $cursor
     * @return \Iterator
     * @since 0.0.1
     * @version 0.0.1
     */
    public function getIterator($cursor = null): \Iterator
    {
        if ($cursor instanceof \Iterator) {
            $cursor = $this->delegate($cursor);
        } else if (is_string($cursor)) {
            $cursor = $this->delegate($this->cursor->runQuery($cursor));
        } else {
            $cursor = $this->delegate($this->cursor->find());
        }
        return $cursor;
    }

    /**
     * Удаление модели
     *
     * @param array|IModel $model
     * @since 0.0.1
     * @version 0.0.1
     */
    public function remove($model)
    {
        $this->selectCollection()->remove($model);
    }

    /**
     * Сохранение модели
     *
     * @param array|IModel $model
     * @since 0.0.1
     * @version 0.0.1
     */
    public function save($model)
    {
        $this->selectCollection()->save($model);
    }

    /**
     * Выбор коллекции
     *
     * @param string $alias
     * @return GDbCollection
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectCollection(string $alias = ""): GDbCollection
    {
        return $this->connection->selectCollection($this->dbName, $this->collectionName, $alias);
    }

    /**
     * Выбор базы данных
     *
     * @return GDbDatabase
     * @since 0.0.1
     * @version 0.0.1
     */
    public function selectDB(): GDbDatabase
    {
        return $this->connection->selectDB($this->dbName);
    }

    /**
     * Устновка названия коллекции, в которой располагаются модели
     *
     * @param string $collectionName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setCollectionName(string $collectionName)
    {
        $this->_collectionName = $collectionName;
    }

    /**
     * Устновка подключения к серверу базы данных
     *
     * @param GDbConnection $connection
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setConnection(GDbConnection $connection)
    {
        $this->_connection = $connection;
    }

    /**
     * Установка названия компонента, выполняющего подключение к
     * серверу базы данных
     *
     * @param string $connectionName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setConnectionName(string $connectionName)
    {
        $this->_connectionName = $connectionName;
    }

    /**
     * Установка названия базы данных с коллекциями моделей
     *
     * @param string $dbName
     * @return void
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setDbName(string $dbName)
    {
        $this->_dbName = $dbName;
    }

    /**
     * Обновление существующей модели
     *
     * @param $model
     * @return int
     * @since 0.0.1
     * @version 0.0.1
     */
    public function update($model)
    {
        $result = 0;
        if ($model instanceof IModel) {
            if ($model->onBeforeUpdate()) {
                $result = $this->selectCollection()->update($model);
                $model->onAfterUpdate();
            }
        } else {
            $result = $this->selectCollection()->update($model);
        }
        return $result;
    }
}
