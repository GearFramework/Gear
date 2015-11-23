<?php

namespace gear\interfaces;

/** 
 * Интерфейс идентификации пользователя
 * 
 * @package Arquivo Corporation Edition
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 0.0.1
 * @since 03.08.2013
 */
interface IIdentity
{
    /**
     * Вызов метода идентифкации
     * 
     * @access public
     * @return object
     */
    public function __invoke();
    
    /**
     * Метод идентификации пользователя
     * 
     * @access public
     * @return array of user properties
     */
    public function identity();
    
    /**
     * Метод выхода пользователя
     * 
     * @access public
     * @return void
     */
    public function logout();
}