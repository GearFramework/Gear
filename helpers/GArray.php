<?php

namespace gear\helpers;
use gear\Core;
use gear\library\GHelper;
use gear\library\GException;

/** 
 * Хелпер для работы с массивами
 * 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 22.12.2014
 */
class GArray extends GHelper
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * @param array|object $array
     * @return bool
     */
    public static function isAssoc($array)
    {
        if (is_object($array) && method_exists($array, 'isAssoc'))
            return $array->isAssoc();
        else
        if (is_array($array))
        {
            $keys = array_keys($array);
            return array_keys($keys) !== $keys;
        }
        else
            self::e('Invalid argument');
    }
}

/** 
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 22.12.2014
 */
class ArrayException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
