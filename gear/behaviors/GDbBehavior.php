<?php

namespace gear\behaviors;
use \gear\Core;
use \gear\library\GBehavior;

/**
 * Набор методов расширяющих поведение объектов до возможности
 * использования подключения к базам данных
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 09.05.2013
 */
class GDbBehavior extends GBehavior
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Получение названия компонента, выполняющего подключение к
     * базе данных
     * 
     * @access public
     * @return string
     */
    public function getConnectionName()
    {
        return $this->getOwner()->connectionName;
    }
    
    /**
     * Получение названия базы данных
     * 
     * @access public
     * @return string
     */
    public function getDbName()
    {
        return $this->getOwner()->dbName;
    }
    
    /**
     * Получение названия таблицы
     * 
     * @access public
     * @return string
     */
    public function getCollectionName()
    {
        return $this->getOwner()->collectionName;
    }
    
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
            $this->getOwner()->e('Компонент базы данных не найден');
    }
}
