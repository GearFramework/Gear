<?php

namespace gear\interfaces;

/** 
 * Интерфейс поведений
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 03.08.2013
 */
interface IBehavior
{
    /**
     * Метод, который выполняется во время подключения поведения.
     * Создаёт и возвращает экземпляр поведения
     * 
     * @access public
     * @static
     * @param object $owner
     * @return object
     */
    public static function attach($owner);
    
    /**
     * @access public
     * @return mixed
     */
    public function __invoke();
}
