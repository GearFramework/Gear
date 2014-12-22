<?php

namespace gear\library;

use gear\Core;
use gear\library\GException;

/** 
 * Класс хелперов
 * 
 * @package Gear Framework
 * @abstract
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 22.12.2014
 */
abstract class GHelper
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    protected function __construct() {}

    protected function __clone() {}
    
    public static function e($message, array $params = [], $type = 0)
    {
        $path = str_replace('\\', '/', get_called_class());
        $class = str_replace('/', '\\', dirname($path) . '/' . substr(basename($path), 1) . 'Exception');
        throw new $class($message, $params, $type);
    }
}

/** 
 * Исключения хелперов
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 22.12.2014
 */
class HelperException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    protected function __construct() {}

    protected function __clone() {}
}
