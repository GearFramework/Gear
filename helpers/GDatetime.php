<?php

namespace gear\helpers;
use gear\Core;
use gear\library\GException;

/**
 * хелпера GDatetime
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 27.05.2014
 */
class GDatetime
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */

    /**
     * Получение текущей даты с форматированием по указанному шаблону
     *
     * @access public
     * @static
     * @param string $format
     * @param bool $natural
     * @return string
     */
    public static function now($format = null, $natural = false)
    {
        return self::$_calculate(time(), $format ? $format : self::$format);
    }

    /**
     * Получение завтрашней даты с форматированием по указанному шаблону
     *
     * @access public
     * @static
     * @param string $format
     * @param bool $natural
     * @return string
     */
    public static function tomorrow($format = null, $natural = false)
    {
        return self::_calculate(strtotime(time(), '+1 day'), $format ? $format : self::$format);
    }

    /**
     * Получение вчерашней даты с форматированием по указанному шаблону
     *
     * @access public
     * @static
     * @param string $format
     * @param bool $natural
     * @return string
     */
    public static function yesterday($format = null, $natural = false)
    {
        return self::_calculate(strtotime(time(), '-1 day'), $format ? $format : self::$format);
    }

    /**
     * Возвращает true если дата соответствует високосному году, иначе false
     *
     * @access public
     * @static
     * @param integer|string $time
     * @return boolean
     */
    public static function isLeap($time)
    {
        if (!is_numeric($time))
            $time = strtotime($time);
        return (bool)date('L', $time);
    }

    /**
     * Возвращает true если указанный год високосный, иначе false
     *
     * @access public
     * @static
     * @param integer $year
     * @return boolean
     */
    public static function isLeapYear($year)
    {
        return (int)$year % 4 ? false : true;
    }

    /**
     * Преобразует текстовое представление даты на английском языке в метку
     * времени Unix
     *
     * @access public
     * @static
     * @param string $time
     * @param string $format
     * @param boolean $natural
     * @return integer|string
     */
    public static function strToTime($time, $format = null, $natural = false)
    {
        return $format ? self::_calculate(strtotime($time), $format, $natural) : strtotime($time);
    }

    public static function format($time, $format, $natural = false)
    {
        return self::_calculate($time, $format, $natural);
    }

    public static function getWeeks()
    {
        $class = self::$localePath . '\\' . self::$locale;
        return $class::getFullWeeks();
    }

    public static function firstDayOfWeek($time)
    {
        if (!is_numeric($time))
            $time = strtotime($time);
        $class = self::$localePath . '\\' . self::$locale;
        $number = $class::getNumberDayOfWeek($time);
    }

    /**
     * Форматирование даты по указанному шаблону
     *
     * @access private
     * @static
     * @param integer|string $time
     * @param string $format
     * @param bool $natural
     * @return string
     */
    private static function _calculate($time, $format, $natural = false)
    {
        if (!is_numeric($time))
            $time = strtotime($time);
        $class = self::$localePath . '\\' . self::$locale;
        $defaultTokens = array('a', 'A', 'B', 'c', 'd', 'D', 'e', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'O', 'P', 'r', 's', 'S', 't', 'T', 'u', 'U', 'w', 'W', 'y', 'Y', 'z', 'Z');
        $natural = (int)$natural ? 1 : 0;
        $result = '';
        foreach(preg_split('//', $format, 0, PREG_SPLIT_NO_EMPTY) as $token)
        {
            if (in_array($token, $defaultTokens, true))
                $result .= date($token, $time);
            else
            if (in_array($token, $class::$registerTokens, true))
                $result .= $class::getTokenValue($token, $time, $natural);
            else
                $result .= $token;
        }
        return $result;
    }
}

/**
 * Исключения хелпера GDatetime
 *
 * @package Gear Framework
 * @author Kukushkin Denis
 * @copyright Kukushkin Denis 2013
 * @version 0.0.1
 * @since 27.05.2014
 */
class DatetimeException extends GException
{
    /* Const */
    /* Private */
    /* Protected */
    /* Public */
}
