<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GPlugin;
use \gear\library\GComponent;
use \gear\library\GException;
use \gear\interfaces\IDbPlugin;

/**
 * Класс описывающий плагин для работы с базой данных
 *
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 03.08.2013
 */
abstract class GDbPlugin extends GPlugin implements IDbPlugin
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
     * Возвращает соединение
     *
     * @access public
     * @param boolean $autoSelectDb
     * @param boolean $autoSelectCollection
     * @throws DbComponentCollection
     * @return GDbConnection|GDbDatabase|GDbCollection
     */
    public function getConnection($autoSelectDb = true, $autoSelectCollection = true)
    {
        $connection = null;
        $connectionName = $this->getConnectionName();
        if (is_string($connectionName))
            $connection = Core::c($connectionName);
        else
        if (is_array($connectionName))
        {
            list($module, $component) = $connectionName;
            $connection = Core::m($module)->$component;
        }
        if ($connection)
        {
            if ($autoSelectDb && !$autoSelectCollection)
                return $connection->selectDB($this->getDbName());
            else
            if ($autoSelectDb && $autoSelectCollection)
                return $connection->selectCollection($this->getDbName(), $this->getCollectionName());
            else
                return $connection;
        }
        else
            $this->e('Компонент базы данных не найден');
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
    public function getConnectionName() { return $this->connectionName; }

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
    public function getDbName() { return $this->dbName; }

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
    public function getCollectionName() { return $this->collectionName; }
}

/**
 * Класс Исключений плагина
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
class DbPluginException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
