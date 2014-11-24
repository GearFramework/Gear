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
    const IS_TAG = 1;
    const IS_ID = 2;
    const IS_CLASS = 3;
    const IS_PARAMS = 4;
    const IS_PARAMNAME = 5;
    const IS_PARAMVALUE = 6;
    const IS_ESCAPE = 7;
    const IS_END = 10;
    /* Private */
    private static $_tagMuliValues = ['select'];
    /* Protected */
    /* Public */
    
    public static function __callStatic($name, $args)
    {
        $res = '';
        static::_prepareSelector(trim($name), $res);
        if (!count($args))
            $args = [in_array($name, self::$_tagMuliValues) ? [] : null, [], []];
        if (!empty($res[self::IS_PARAMNAME]))
        {
            foreach($res[self::IS_PARAMNAME] as $index => $value)
                $args[1][strtolower($value)] = $res[self::IS_PARAMVALUE][$index];
        }
        if (empty($res[self::IS_TAG]))
            static::e('Unknown tag');
        $name = $res[self::IS_TAG];
        if (isset($res[self::IS_ID]))
            $args[1]['id'] = $res[self::IS_ID];
        if (!empty($res[self::IS_CLASS]))
        {
            foreach($res[self::IS_CLASS] as $className)
                $args[1]['class'][] = $className;
        }
        return call_user_func_array([__CLASS__, $name], $args);
    } 
    
    protected static function _prepareSelector($selector, &$return, $offset = 0, $state = self::IS_TAG)
    {
        if (!$offset)
            $return = 
            [
                self::IS_TAG => '', 
                self::IS_CLASS => [], 
                self::IS_ID => '', 
                self::IS_PARAMS => [],
                self::IS_PARAMNAME => [],
                self::IS_PARAMVALUE => [],
            ];
        if (isset($selector[$offset]) && $state !== self::IS_END)
        {
            if ($selector[$offset] === '#')
                $state = self::IS_ID;
            else
            if ($selector[$offset] === '.')
            {
                $state = self::IS_CLASS;
                $return[$state][] = '';
            }
            else
            if ($selector[$offset] === '[')
            {
                $state = self::IS_PARAMS;
                $return[$state][] = '';
            }
            else
            if ($selector[$offset] === ']')
                $state = self::IS_END;
            else
            if (($selector[$offset] === '=' && $state === self::IS_PARAMNAME) || 
                ($selector[$offset] === '"' && $state === self::IS_PARAMVALUE)) {}
            else
            if ($selector[$offset] === ',' && $state === self::IS_PARAMVALUE)
                $state = self::IS_PARAMS;
            else
            if ($selector[$offset] === '"' && $state === self::IS_PARAMNAME)
            {
                $state = self::IS_PARAMVALUE;
                $return[$state][] = '';
            }
            else
            {
                if ($state === self::IS_PARAMS)
                {
                    if ($selector[$offset] !== ' ')
                    {
                        $state = self::IS_PARAMNAME;
                        $return[$state][] = $selector[$offset];
                    }
                }
                else
                if ($state === self::IS_PARAMNAME || $state === self::IS_PARAMVALUE)
                    $return[$state][count($return[$state]) - 1] .= $selector[$offset];
                else
                if ($state === self::IS_CLASS)
                    $return[$state][count($return[$state]) - 1] .= $selector[$offset];
                else
                    $return[$state] .= $selector[$offset];
            }
            static::_prepareSelector($selector, $return, $offset + 1, $state);
        }
	}
    
    public static function div($content, $attributes = [], $styles = [])
    {
        foreach($attributes as $name => &$value)
        {
            if (is_array($value))
                $value = $name . '="' . implode(' ', $value) . '"';
            else
                $value = $name . '="' . htmlspecialchars($value) . '"';
        }
        unset($value);
        echo '<div' . (!empty($attributes) ? ' ' . implode(' ', $attributes) : '') . '>' . $content . '</div>';
    }
}
