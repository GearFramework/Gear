<?php

namespace gear\interfaces;

/** 
 * Интерфейс компонентов
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 */
interface IComponent
{
    /**
     * Установка компонента
     * 
     * @access public
     * @static
     * @param string|array $config
     * @param array $properties
     * @param null|object $owner
     * @return GComponent
     */
    public static function install($config, array $properties = array(), $owner = null);
    
    /**
     * Конфигурирование класса компонента
     * 
     * @access public
     * @static
     * @param string|array $config
     * @return void
     */
    public static function init($config);
    
    /**
     * Получение экхемпляра компонента
     * 
     * @access public
     * @static
     * @param array $properties
     * @param nulll|object $owner
     * @return GComponent
     */
    public static function it(array $properties = array(), $owner = null);

    /**
     * Возвращает true, если компонент может быть перегружен, иначе false
     * 
     * @access public
     * @return boolean
     */
    public function isOverride();
}