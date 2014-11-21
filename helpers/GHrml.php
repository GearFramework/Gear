<?php

namespace gear\helpers;
use gear\Core;
use gear\library\GObject;

/**
 * Класс для работы с HTML
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 1.0.0
 * @since 21.11.2014
 */
class GHtml extends GObject
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
    
    public static function __callStatic($name, $args)
    {
        if (strpos($name, '#'))
        {
            list($name, $id) = explode('#', $name);
            if (count($args) > 1)
                $args[1]['id'] = $name;
            else
            if (count($args) == 1)
                $args[]['id'] = $name;
            else
                $args = [null, ['id' => $name]];
        }
        else
        if (strpos($name, '.'))
        {
            list($name, $class) = explode('.', $name);
            array_unshift($args, $class)
        }
        else
            parent::__callStatic($name, $args);
        return call_user_func_array([__CLASS__, $name], $args);
    } 
    
    protected static function _prepareArgs($args)
    {
        switch(count($args))
        {
            case 0 : return [null, [], []];
            case 1 : return [$args[0], [], []];
            case 2 : return [$args[0], $args[1], []];
            case 3 : return [$args[0], $args[1], $args[2]];
        }
    }
    
    public static function div()
    {
        list($class, $attributes, $styles) = self::_prepareArgs(func_get_args());
    }
}
