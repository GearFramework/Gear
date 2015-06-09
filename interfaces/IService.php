<?php

namespace gear\interfaces;

/**
 * Интерфейс сервисов
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 29.12.2014
 * @release 1.0.0
 */
interface IService
{
    /**
     * Установка сервиса
     *
     * @access public
     * @static
     * @param string|array $config
     * @return GService
     */
    public static function install($config);

    /**
     * Конфигурирование класса сервиса
     *
     * @access public
     * @static
     * @param string|array $config
     * @return void
     */
    public static function init($config);

    /**
     * Получение экхемпляра сервиса
     *
     * @access public
     * @static
     * @param array $properties
     * @param nulll|object $owner
     * @return GService
     */
    public static function it(array $properties = array(), $owner = null);

    /**
     * Возвращает true, если сервис может быть перегружен, иначе false
     *
     * @access public
     * @return boolean
     */
    public function isOverride();
}
