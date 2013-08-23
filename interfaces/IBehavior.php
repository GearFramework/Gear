<?php

namespace gear\interfaces;

/** 
 * Интерфейс поведений
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
interface IBehavior
{
    /**
     * Метод, который выполняется во время подключения поведения.
     * 
     * @access public
     * @static
     * @param GObject $owner
     * @return GBehavior
     */
    public static function attach($owner);
}