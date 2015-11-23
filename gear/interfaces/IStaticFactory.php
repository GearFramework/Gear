<?php

namespace gear\interfaces;

/** 
 * Интерфейс фабрики объектов
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 18.06.2014
 */
interface IStaticFactory
{
    /**
     * Возвращает созданный объект
     * 
     * @access public
     * @param array|\Closure $properties
     * @return object
     */
    public static function factory($properties = array());

    /**
     * Установка параметров создаваемых объектов
     *
     * @access public
     * @param array $factory
     */
    public static function setFactory(array $factory);

    /**
     * Получение параметров создаваемых объектов
     *
     * @access public
     * @return array
     */
    public static function getFactory();
}
