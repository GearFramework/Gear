<?php

namespace Gear\Library\Db;

use Gear\Core;
use Gear\Interfaces\DbConnectionInterface;
use Gear\Interfaces\DbCursorInterface;
use Gear\Interfaces\DbStorageComponentInterface;
use Gear\Interfaces\FactoryInterface;
use Gear\Library\GComponent;
use Gear\Traits\Factory\DelegateFactoryTrait;
use Gear\Traits\Factory\FactoryTrait;

/**
 * Бибилиотека для компонентов, работающих с данными в базе данных
 *
 * @package Gear Framework
 *
 * @property string alias
 * @property string collectionName
 * @property DbConnectionInterface $connection
 * @property string connectionName
 * @property DbCursorInterface cursor
 * @property string dbName
 * @property array defaultParams
 * @property string primaryKey
 *
 * @author Kukushkin Denis
 * @copyright 2016 Kukushkin Denis
 * @license http://www.spdx.org/licenses/MIT MIT License
 * @since 0.0.1
 * @version 0.0.2
 */
abstract class GDbStorageComponent extends GComponent implements \IteratorAggregate, FactoryInterface, DbStorageComponentInterface
{
    /* Traits */
    use DelegateFactoryTrait;
    use FactoryTrait;
    /* Const */
    /* Private */
    /* Protected */
    protected $_factoryProperties = [
        'class' => '\Gear\Library\GModel',
    ];
    protected $_alias = '';
    protected $_connection = null;
    protected $_connectionName = 'db';
    protected $_dbName = '';
    protected $_collectionName = '';
    protected $_defaultParams = [];
    protected $_primaryKeyName = 'id';
    /* Public */

    /**
     * Возвращает алиас для коллекции
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
     * @return DbConnectionInterface
     * @throws \CoreException
     * @since 0.0.1
     * @version 0.0.2
     */
    public function getConnection(): DbConnectionInterface
    {
        /** @var DbConnectionInterface _connection */
        if (!$this->_connection) {
            $this->connection = Core::c($this->connectionName);
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
     * Установка алиаса для коллекции
     *
     * @param string $alias
     * @since 0.0.1
     * @version 0.0.1
     */
    public function setAlias(string $alias)
    {
        $this->_alias = $alias;
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
     * @param DbConnectionInterface $connection
     * @return void
     * @since 0.0.1
     * @version 0.0.2
     */
    public function setConnection(DbConnectionInterface $connection)
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
}
