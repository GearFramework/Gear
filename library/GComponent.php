<?php

namespace gear\library;

use gear\Core;
use gear\library\GService;
use gear\library\GException;
use gear\interfaces\IComponent;

/** 
 * Класс компонентов
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 01.08.2013
 * @php 5.3.x
 */
abstract class GComponent extends GService implements IComponent
{
    /* Const */
    /* Private */
    /* Protected */
    protected static $_config = array();
    protected static $_init = false;
    /* Public */

    /**
     * Разрешено клонирование компонентов
     *
     * @access public
     * @return void
     */
    public function __clone() {}
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
