<?php

namespace gear\library\db;

use gear\Core;
use gear\interfaces\IModel;
use gear\library\GComponent;
use gear\traits\TDelegateFactory;
use gear\traits\TFactory;

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
    /* Public */

    public function add($model)
    {
        $this->selectCollection()->insert($model);
    }

    public function all()
    {
        return $this->getIterator($this->find());
     }

    public function byPk($pkValue)
    {
        $class = $this->factory['class'];
        $result = $this->selectCollection()->findOne([$class::primaryKey() => "'$pkValue'"]);
        return $result ? $this->factory($result) : $result;
    }

    public function find($criteria = [], $fields = [])
    {
        return $this->getIterator($this->selectCollection()->find($criteria, $fields));
    }

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

    public function remove($model)
    {
        $this->selectCollection()->remove($model);
    }

    public function save($model)
    {
        $this->selectCollection()->save($model);
    }

    public function selectCollection(): GDbCollection
    {
        return $this->connection->selectCollection($this->dbName, $this->collectionName);
    }

    public function selectDB(): GDbDatabase
    {
        return $this->connection->selectDB($this->dbName);
    }

    public function setCollectionName(string $collectionName)
    {
        $this->_collectionName = $collectionName;
    }

    public function setConnection(GDbConnection $connection)
    {
        $this->_connection = $connection;
    }

    public function setConnectionName(string $connectionName)
    {
        $this->_connectionName = $connectionName;
    }

    public function setDbName(string $dbName)
    {
        $this->_dbName = $dbName;
    }

    public function update($model)
    {
        $result = false;
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
