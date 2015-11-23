<?php

namespace gear\interfaces;
use \gear\interfaces\IComponent;

/** 
 * Интерфейс компонентов работающих с базами данных
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 01.08.2013
 * @release 1.0.0
 */
interface IDbComponent extends IComponent
{

    /**
     * Возвращает соединение с сервером базы данных
     *
     * @access public
     * @param boolean $autoSelectDb
     * @param boolean $autoSelectCollection
     * @return \gear\library\db\GDbConnection
     */
    public function getDbConnection($autoSelectDb = true, $autoSelectCollection = true);

    /**
     * Возвращает соединение
     * 
     * @access public
     * @throws DbComponentCollection
     * @return GDbConnection || GDbDatabase || GDbCollection
     */
    public function getConnection();

    /**
     * Возвращает объект базы данных
     *
     * @access public
     * @return \gear\library\db\GDbDatabase
     */
    public function getDb();


    /**
     * Возвращает объект таблицу (коллекцию)
     *
     * @access public
     * @return \gear\library\db\GDbCollection
     */
    public function getCollection();
    
    /**
     * Возвращает название компонента, выполняющего соединение с сервером базы
     * данных
     * 
     * @access public
     * @return string
     */
    public function getConnectionName();

    /**
     * Возвращает название базы данных
     * 
     * @access public
     * @return string
     */
    public function getDbName();

    /**
     * Возвращает название таблицы
     * 
     * @access public
     * @return string
     */
    public function getCollectionName();
}
