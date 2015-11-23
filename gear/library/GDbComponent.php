<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GStorageComponent;
use \gear\library\GException;
use \gear\interfaces\IDbComponent;

/**
 * Класс компонентов, работающих с базами данных
 *
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 01.08.2013
 * @php 5.4.x or higher
 * @release 1.0.0
 */
abstract class GDbComponent extends GStorageComponent implements IDbComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = [];
    protected static $_init = false;
    protected $_connectionName = 'connection';
    protected $_dbName = 'database';
    protected $_collectionName = 'table';
    /* Public */

    /**
     * Возвращает соединение с базой данных
     *
     * @access public
     * @return object
     */
    public function storage() { return $this->getDbConnection(); }

    /**
     * Возвращает соединение с сервером базы данных
     *
     * @access public
     * @param boolean $autoSelectDb
     * @param boolean $autoSelectCollection
     * @return object
     */
    public function getDbConnection($autoSelectDb = true, $autoSelectCollection = true)
    {
        $connection = $this->getConnection();
        if ($autoSelectDb && !$autoSelectCollection)
            return $connection->selectDB($this->getDbName());
        else
        if ($autoSelectDb && $autoSelectCollection)
            return $connection->selectCollection($this->getDbName(), $this->getCollectionName());
        else
            return $connection;
    }

    /**
     * Возвращает компонент для соединение базой данных
     *
     * @access public
     * @throws DbComponentCollection
     * @return GDbConnection|GDbDatabase|GDbCollection
     */
    public function getConnection()
    {
        $connection = null;
        $connectionName = $this->getConnectionName();
        if (is_string($connectionName))
            $connection = Core::c($connectionName);
        else
        if (is_array($connectionName))
        {
            list($module, $component) = $connectionName;
            $connection = Core::m($module)->с($component);
        }
        if (!$connection)
            throw $this->exceptionDbComponentNotFound(['dbComponent' => is_array($connectionName) ? implode('->', $connectionName) : $connectionName]);
        return $connection;
    }

    /**
     * Возвращает объект базы данных
     *
     * @access public
     * @return \gear\library\db\GDbDatabase
     */
    public function getDb()
    {
        return $this->getConnection(false, false)->selectDb($this->getDbName());
    }

    /**
     * Возвращает объект таблицу (коллекцию)
     *
     * @access public
     * @return \gear\library\db\GDbCollection
     */
    public function getCollection()
    {
        return $this->getDb()->selectCollection($this->getCollectionName());
    }

    /**
     * Установка названия соединения с сервером баз данных
     *
     * @access public
     * @param string $connectionName
     * @return void
     */
    public function setConnectionName($connectionName) { $this->_connectionName = $connectionName; }

    /**
     * Возвращает название компонента, выполняющего соединение с сервером базы
     * данных
     *
     * @access public
     * @return string
     */
    public function getConnectionName() { return $this->_connectionName; }

    /**
     * Установка названия базы данных
     *
     * @access public
     * @param string $dbName
     * @return void
     */
    public function setDbName($dbName) { $this->_dbName = $dbName; }

    /**
     * Возвращает название базы данных
     *
     * @access public
     * @return string
     */
    public function getDbName() { return $this->_dbName; }

    /**
     * Установка названия таблицы
     *
     * @access public
     * @param string $collectionName
     * @return void
     */
    public function setCollectionName($collectionName) { $this->_collectionName = $collectionName; }

    /**
     * Возвращает название таблицы
     *
     * @access public
     * @return string
     */
    public function getCollectionName() { return $this->_collectionName; }
}
