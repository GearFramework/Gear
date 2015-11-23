<?php

namespace gear\library;

use gear\library\GComponent;
use gear\library\GException;
use gear\library\TEvents;
use gear\interfaces\IPlugin;

/**
 * Класс описывающий плагин
 *
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis
 * @version 1.0.0
 * @since 01.08.2013
 * @php 5.4.x or higher
 * @release 1.0.0
 */
abstract class GPlugin extends GComponent implements IPlugin
{
    /* Traits */
    use TEvents;
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config =
    [
        'events' => [],
        'behaviors' => [],
        'plugins' =>
        [
            'view' => ['class' => '\gear\plugins\gear\GView'],
        ],
    ];
    protected static $_init = false;
    /* Public */

    /**
     * Метод, который выполняется во время инсталляции плагина.
     * Запускает инициализацию класса (конфигурирование) и возвращает
     * инстанс.
     *
     * @access public
     * @static
     * @param array|string as path to file $config
     * @param array $properties
     * @param null|object $owner
     * @return GPlugin
     */
    public static function install($config = [], array $properties = [], $owner = null)
    {
        static::checkDependency($owner);
        return parent::install($config, $properties, $owner);
    }

    /**
     * Проверка зависимости класса владельца
     *
     * @access public
     * @static
     * @param object $owner
     * @return boolean
     */
    public static function checkDependency($owner)
    {
        $dependencyClass = static::i('dependency');
        if (!(!$dependencyClass || ($dependencyClass && $owner instanceof $dependencyClass)))
            throw static::exceptionService('Owner has been instanced of ":ownerClass"', ['ownerClass' => $dependencyClass]);
    }
}
