<?php

namespace gear\library;
use \gear\Core;
use \gear\library\GObject;
use \gear\library\GException;

/** 
 * Базовый класс моделей 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 */
class GModel extends GObject
{
    /* Const */
    /* Private */
    /* Protected */
    protected $_owner = null;
    /* Public */
    /**
     * Конструктор класса
     * Принимает ассоциативный массив свойств объекта и их значений
     * 
     * @access public
     * @param array $properties
     * @return void
     */
    public function __construct(array $properties = array())
    {
        parent::__construct($properties);
    }
    
    /**
     * Клонирование
     * 
     * @access public
     * @return void
     */
    public function __clone() { parent::__clone(); }
}

/** 
 * Исключения базовой модели 
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 */
class ModelException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
