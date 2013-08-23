<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GComponent;
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
 */
abstract class GDbComponent extends GComponent implements IDbComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array();
    protected static $_init = false;
    /* Public */
    public $connectionName = 'connection';
    public $dbName = 'database';
    public $collectionName = 'table';
    
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
     * Возвращает название компонента, выполняющего соединение с сервером базы
     * данных
     * 
     * @access public
     * @return string
     */
    public function getConnectionName() { return $this->connectionName; }

    /**
     * Возвращает название базы данных
     * 
     * @access public
     * @return string
     */
    public function getDbName() { return $this->dbName; }

    /**
     * Возвращает название таблицы
     * 
     * @access public
     * @return string
     */
    public function getCollectionName() { return $this->collectionName; }
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
