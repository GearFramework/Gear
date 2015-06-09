<?php

namespace gear\interfaces;

/**
 * Интерфейс моделей с фиксированной схемой полей 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 * @release 1.0.0
 */
interface ISchema
{
    /**
     * Метод должен возвращать схему описания полей модели
     * 
     * @access public
     * @return array
     */
    public function getSchema();
    
    /**
     * Метод должен возвращать массив названий полей модели
     * 
     * @access public
     * @return array
     */
    public function getSchemaNames();
    
    /**
     * Метод должен возвращать массив значений соответствующих полей
     * модели в виде ассоциативного массива
     * 
     * @access public
     * @return array
     */
    public function getSchemaValues();
}
