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
class GArray extends GHeader
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    public static function isAssoc($array)
    {
        if (is_object($array) && $array instanceof \Traversable)
            return $array->isAssoc();
        else
        if (is_array($array))
        {
            $keys = array_keys($array);
            return array_keys($keys) !== $keys;
        }
        else
            self::e('Ivalid argument');
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
