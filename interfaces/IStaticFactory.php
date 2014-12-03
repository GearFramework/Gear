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
     * @param array $properties
     * @return object
     */
    public static function factory(array $properties = []);
}
