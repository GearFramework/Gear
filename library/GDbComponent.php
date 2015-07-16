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
 * @version 0.0.1
 * @since 01.08.2013
 * @php 5.3.x
 */
abstract class GDbComponent extends GStorageComponent implements IDbComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array();
    protected static $_init = false;
    protected $_connectionName = 'connection';
    protected $_dbName = 'database';
    protected $_collectionName = 'table';
    /* Public */

    /**
     * Возвращает соединение с сервером базы данных
     *
     * @access public
     * @param boolean $autoSelectDb
     * @param boolean $autoSelectCollection
     * @return object
     */
    public function getConnection($autoSelectDb = true, $autoSelectCollection = true)
    {
        $connection = $this->getDbConnection();
        if ($autoSelectDb && !$autoSelectCollection)
            return $connection->selectDB($this->getDbName());
        else
        if ($autoSelectDb && $autoSelectCollection)
            return $connection->selectCollection($this->getDbName(), $this->getCollectionName());
        else
            return $connection;
    }

    /**
     * Возвращает соединение базой данных
     *
     * @access public
     * @throws DbComponentCollection
     * @return GDbConnection|GDbDatabase|GDbCollection
     */
    public function getDbConnection()
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
            $this->e('Компонент базы данных не найден');
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

/**
 * Исключения компонента
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 03.08.2013
 */
class DbComponentException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
