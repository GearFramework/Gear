<?php

namespace gear\interfaces;

/** 
 * Интерфейс модулей
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 */
interface IModule
{
    /**
     * Установка модуля
     * 
     * @access public
     * @static
     * @param string|array $config
     * @param array $properties
     * @param null|object $owner
     * @return GComponent
     */
    public static function install($config, array $properties = array());
    
    /**
     * Конфигурирование класса модуля
     * 
     * @access public
     * @static
     * @param string|array $config
     * @return void
     */
    public static function init($config);
    
    /**
     * Получение экземпляра модуля
     * 
     * @access public
     * @static
     * @param array $properties
     * @param nulll|object $owner
     * @return GComponent
     */
    public static function it(array $properties = array());
    
    /**
     * Получение компонента, харегистрированного модулем
     * 
     * @access public
     * @param string $name
     * @return GComponent
     */
    public function c($name);
    
    /**
     * Возвращает запись о компоненте модуля, иначе false если компонент
     * с указанным именем не зарегистрирован
     * 
     * @access public
     * @param string $name
     * @return array|false
     */
    public function isComponentRegistered($name);

    /**
     * Возвращает true, если модуль может быть перегружен, иначе false
     * 
     * @access public
     * @return boolean
     */
    public function isOverride();
}
