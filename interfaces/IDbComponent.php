<?php

namespace gear\interfaces;
use \gear\interfaces\IComponent;

/** 
 * Интерфейс компонентов работающих с базами данных
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 */
interface IDbComponent extends IComponent
{
    /**
     * Возвращает соединение
     * 
     * @access public
     * @param boolean $autoSelectDb
     * @param boolean $autoSelectCollection
     * @throws DbComponentCollection
     * @return GDbConnection|GDbDatabase|GDbCollection
     */
    public function getConnection($autoSelectDb = true, $autoSelectCollection = true);
    
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
