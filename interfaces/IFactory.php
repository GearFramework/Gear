<?php

namespace gear\interfaces;

/** 
 * Интерфейс фабрики объектов
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 18.06.2014
 * @release 1.0.0
 */
interface IFactory
{
    /**
     * Возвращает созданный объект
     * 
     * @access public
     * @param array|\Closure $properties
     * @return object
     */
    public function factory($properties = array());
}
