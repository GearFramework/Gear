<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GObject;
use \gear\library\GException;
use \gear\interfaces\IComponent;

/** 
 * Класс компонентов
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 */
abstract class GComponent extends GObject implements IComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array();
    /* Public */
    
    public function __clone() {}
    
    /**
     * Получение экхемпляра компонента
     * 
     * @access public
     * @static
     * @param array $properties
     * @param nulll|object $owner
     * @return GComponent
     */
    public static function it(array $properties = [], $owner = null)
    {
        if ($owner)
            $properties['owner'] = $owner;
        return new static($properties);
    }
}

/** 
 * Исключения компонента
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 */
class ComponentException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
